<?php
/**
 *  @class: InputFilter (PHP4 & PHP5, with comments)
 * @project: PHP Input Filter
 * @date: 10-05-2005
 * @version: 1.2.2_php4/php5
 * @author: Daniel Morris
 * @contributors: Gianpaolo Racca, Ghislain Picard, Marco Wandschneider, Chris
 * Tobin and Andrew Eddie.
 *
 * Modification by Louis Landry
 *
 * @copyright: Daniel Morris
 * @email: dan@rootcube.com
 * @license: GNU General Public License (GPL)
 */
class InputFilter
{
    var $tagsArray; // default = empty array
    var $attrArray; // default = empty array

    var $tagsMethod; // default = 0
    var $attrMethod; // default = 0

    var $xssAuto; // default = 1
    var $tagBlacklist = array ('applet', 'body', 'bgsound', 'base', 'basefont', 'embed', 'frame', 'frameset', 'head', 'html', 'id', 'iframe', 'ilayer', 'layer', 'link', 'meta', 'name', 'object', 'script', 'style', 'title', 'xml');
    var $attrBlacklist = array ('action', 'background', 'codebase', 'dynsrc', 'lowsrc'); // also will strip ALL event handlers

    /**
     * Constructor for inputFilter class. Only first parameter is required.
     *
     * @access  protected
     * @param   array   $tagsArray  list of user-defined tags
     * @param   array   $attrArray  list of user-defined attributes
     * @param   int     $tagsMethod WhiteList method = 0, BlackList method = 1
     * @param   int     $attrMethod WhiteList method = 0, BlackList method = 1
     * @param   int     $xssAuto    Only auto clean essentials = 0, Allow clean
     * blacklisted tags/attr = 1
     */
    function inputFilter($tagsArray = array (), $attrArray = array (), $tagsMethod = 0, $attrMethod = 0, $xssAuto = 1)
    {
        /*
         * Make sure user defined arrays are in lowercase
         */
        $tagsArray = array_map('strtolower', (array) $tagsArray);
        $attrArray = array_map('strtolower', (array) $attrArray);

        /*
         * Assign member variables
         */
        $this->tagsArray    = $tagsArray;
        $this->attrArray    = $attrArray;
        $this->tagsMethod   = $tagsMethod;
        $this->attrMethod   = $attrMethod;
        $this->xssAuto      = $xssAuto;
    }

    /**
     * Method to be called by another php script. Processes for XSS and
     * specified bad code.
     *
     * @access  public
     * @param   mixed   $source Input string/array-of-string to be 'cleaned'
     * @return mixed    $source 'cleaned' version of input parameter
     */
    function process($source)
    {
        /*
         * Are we dealing with an array?
         */
        if (is_array($source))
        {
            foreach ($source as $key => $value)
            {
                // filter element for XSS and other 'bad' code etc.
                if (is_string($value))
                {
                    $source[$key] = $this->remove($this->decode($value));
                }
            }
            return $source;
        } else
            /*
             * Or a string?
             */
            if (is_string($source) && !empty ($source))
            {
                // filter source for XSS and other 'bad' code etc.
                return $this->remove($this->decode($source));
            } else
            {
                /*
                 * Not an array or string.. return the passed parameter
                 */
                return $source;
            }
    }

    /**
     * Internal method to iteratively remove all unwanted tags and attributes
     *
     * @access  protected
     * @param   string  $source Input string to be 'cleaned'
     * @return  string  $source 'cleaned' version of input parameter
     */
    function remove($source)
    {
        $loopCounter = 0;
        /*
         * Iteration provides nested tag protection
         */
        while ($source != $this->filterTags($source))
        {
            $source = $this->filterTags($source);
            $loopCounter ++;
        }
        return $source;
    }

    /**
     * Internal method to strip a string of certain tags
     *
     * @access  protected
     * @param   string  $source Input string to be 'cleaned'
     * @return  string  $source 'cleaned' version of input parameter
     */
    function filterTags($source)
    {
        /*
         * In the beginning we don't really have a tag, so everything is
         * postTag
         */
        $preTag     = null;
        $postTag    = $source;

        /*
         * Is there a tag? If so it will certainly start with a '<'
         */
        $tagOpen_start  = strpos($source, '<');

        while ($tagOpen_start !== false)
        {

            /*
             * Get some information about the tag we are processing
             */
            $preTag        .= substr($postTag, 0, $tagOpen_start);
            $postTag        = substr($postTag, $tagOpen_start);
            $fromTagOpen    = substr($postTag, 1);
            $tagOpen_end    = strpos($fromTagOpen, '>');

            /*
             * Let's catch any non-terminated tags and skip over them
             */
            if ($tagOpen_end === false)
            {
                $postTag        = substr($postTag, $tagOpen_start +1);
                $tagOpen_start  = strpos($postTag, '<');
                continue;
            }

            /*
             * Do we have a nested tag?
             */
            $tagOpen_nested = strpos($fromTagOpen, '<');
            $tagOpen_nested_end = strpos(substr($postTag, $tagOpen_end), '>');
            if (($tagOpen_nested !== false) && ($tagOpen_nested < $tagOpen_end))
            {
                $preTag        .= substr($postTag, 0, ($tagOpen_nested +1));
                $postTag        = substr($postTag, ($tagOpen_nested +1));
                $tagOpen_start  = strpos($postTag, '<');
                continue;
            }


            /*
             * Lets get some information about our tag and setup attribute pairs
             */
            $tagOpen_nested = (strpos($fromTagOpen, '<') + $tagOpen_start +1);
            $currentTag     = substr($fromTagOpen, 0, $tagOpen_end);
            $tagLength      = strlen($currentTag);
            $tagLeft        = $currentTag;
            $attrSet        = array ();
            $currentSpace   = strpos($tagLeft, ' ');

            /*
             * Are we an open tag or a close tag?
             */
            if (substr($currentTag, 0, 1) == "/")
            {
                // Close Tag
                $isCloseTag     = true;
                list ($tagName) = explode(' ', $currentTag);
                $tagName        = substr($tagName, 1);
            } else
            {
                // Open Tag
                $isCloseTag     = false;
                list ($tagName) = explode(' ', $currentTag);
            }

            /*
             * Exclude all "non-regular" tagnames
             * OR no tagname
             * OR remove if xssauto is on and tag is blacklisted
             */
            if ((!preg_match("/^[a-z][a-z0-9]*$/i", $tagName)) || (!$tagName) || ((in_array(strtolower($tagName), $this->tagBlacklist)) && ($this->xssAuto)))
            {
                $postTag        = substr($postTag, ($tagLength +2));
                $tagOpen_start  = strpos($postTag, '<');
                // Strip tag
                continue;
            }

            /*
             * Time to grab any attributes from the tag... need this section in
             * case attributes have spaces in the values.
             */
            while ($currentSpace !== false)
            {
                $fromSpace      = substr($tagLeft, ($currentSpace +1));
                $nextSpace      = strpos($fromSpace, ' ');
                $openQuotes     = strpos($fromSpace, '"');
                $closeQuotes    = strpos(substr($fromSpace, ($openQuotes +1)), '"') + $openQuotes +1;

                /*
                 * Do we have an attribute to process? [check for equal sign]
                 */
                if (strpos($fromSpace, '=') !== false)
                {
                    /*
                     * If the attribute value is wrapped in quotes we need to
                     * grab the substring from the closing quote, otherwise grab
                     * till the next space
                     */
                    if (($openQuotes !== false) && (strpos(substr($fromSpace, ($openQuotes +1)), '"') !== false))
                    {
                        $attr = substr($fromSpace, 0, ($closeQuotes +1));
                    } else
                    {
                        $attr = substr($fromSpace, 0, $nextSpace);
                    }
                } else
                {
                    /*
                     * No more equal signs so add any extra text in the tag into
                     * the attribute array [eg. checked]
                     */
                    $attr = substr($fromSpace, 0, $nextSpace);
                }

                // Last Attribute Pair
                if (!$attr)
                {
                    $attr = $fromSpace;
                }

                /*
                 * Add attribute pair to the attribute array
                 */
                $attrSet[] = $attr;

                /*
                 * Move search point and continue iteration
                 */
                $tagLeft        = substr($fromSpace, strlen($attr));
                $currentSpace   = strpos($tagLeft, ' ');
            }

            /*
             * Is our tag in the user input array?
             */
            $tagFound = in_array(strtolower($tagName), $this->tagsArray);

            /*
             * If the tag is allowed lets append it to the output string
             */
            if ((!$tagFound && $this->tagsMethod) || ($tagFound && !$this->tagsMethod))
            {
                /*
                 * Reconstruct tag with allowed attributes
                 */
                if (!$isCloseTag)
                {
                    // Open or Single tag
                    $attrSet = $this->filterAttr($attrSet);
                    $preTag .= '<'.$tagName;
                    for ($i = 0; $i < count($attrSet); $i ++)
                    {
                        $preTag .= ' '.$attrSet[$i];
                    }

                    /*
                     * Reformat single tags to XHTML
                     */
                    if (strpos($fromTagOpen, "</".$tagName))
                    {
                        $preTag .= '>';
                    } else
                    {
                        $preTag .= ' />';
                    }
                } else
                {
                    // Closing Tag
                    $preTag .= '</'.$tagName.'>';
                }
            }

            /*
             * Find next tag's start and continue iteration
             */
            $postTag        = substr($postTag, ($tagLength +2));
            $tagOpen_start  = strpos($postTag, '<');
            //print "T: $preTag\n";
        }

        /*
         * Append any code after the end of tags and return
         */
        if ($postTag != '<')
        {
            $preTag .= $postTag;
        }
        return $preTag;
    }

    /**
     * Internal method to strip a tag of certain attributes
     *
     * @access  protected
     * @param   array   $attrSet    Array of attribute pairs to filter
     * @return  array   $newSet     Filtered array of attribute pairs
     */
    function filterAttr($attrSet)
    {
        /*
         * Initialize variables
         */
        $newSet = array ();

        /*
         * Iterate through attribute pairs
         */
        for ($i = 0; $i < count($attrSet); $i ++)
        {
            /*
             * Skip blank spaces
             */
            if (!$attrSet[$i])
            {
                continue;
            }

            /*
             * Split into name/value pairs
             */
            $attrSubSet = explode('=', trim($attrSet[$i]), 2);
            list ($attrSubSet[0]) = explode(' ', $attrSubSet[0]);

            /*
             * Remove all "non-regular" attribute names
             * AND blacklisted attributes
             */
            if ((!eregi("^[a-z]*$", $attrSubSet[0])) || (($this->xssAuto) && ((in_array(strtolower($attrSubSet[0]), $this->attrBlacklist)) || (substr(strtolower($attrSubSet[0]), 0, 2) == 'on'))))
            {
                continue;
            }

            /*
             * XSS attribute value filtering
             */
            if ($attrSubSet[1])
            {
                // strips unicode, hex, etc
                $attrSubSet[1] = str_replace('&#', '', $attrSubSet[1]);
                // strip normal newline within attr value
                $attrSubSet[1] = preg_replace('/\s+/', '', $attrSubSet[1]);
                // strip double quotes
                $attrSubSet[1] = str_replace('"', '', $attrSubSet[1]);
                // [requested feature] convert single quotes from either side to doubles (Single quotes shouldn't be used to pad attr value)
                if ((substr($attrSubSet[1], 0, 1) == "'") && (substr($attrSubSet[1], (strlen($attrSubSet[1]) - 1), 1) == "'"))
                {
                    $attrSubSet[1] = substr($attrSubSet[1], 1, (strlen($attrSubSet[1]) - 2));
                }
                // strip slashes
                $attrSubSet[1] = stripslashes($attrSubSet[1]);
            }

            /*
             * Autostrip script tags
             */
            if (InputFilter :: badAttributeValue($attrSubSet))
            {
                continue;
            }

            /*
             * Is our attribute in the user input array?
             */
            $attrFound = in_array(strtolower($attrSubSet[0]), $this->attrArray);

            /*
             * If the tag is allowed lets keep it
             */
            if ((!$attrFound && $this->attrMethod) || ($attrFound && !$this->attrMethod))
            {
                /*
                 * Does the attribute have a value?
                 */
                if ($attrSubSet[1])
                {
                    $newSet[] = $attrSubSet[0].'="'.$attrSubSet[1].'"';
                }
                elseif ($attrSubSet[1] == "0")
                {
                    /*
                     * Special Case
                     * Is the value 0?
                     */
                    $newSet[] = $attrSubSet[0].'="0"';
                } else
                {
                    $newSet[] = $attrSubSet[0].'="'.$attrSubSet[0].'"';
                }
            }
        }
        return $newSet;
    }

    /**
     * Function to determine if contents of an attribute is safe
     *
     * @access  protected
     * @param   array   $attrSubSet A 2 element array for attributes name,value
     * @return  boolean True if bad code is detected
     */
    function badAttributeValue($attrSubSet)
    {
        $attrSubSet[0] = strtolower($attrSubSet[0]);
        $attrSubSet[1] = strtolower($attrSubSet[1]);
        return (((strpos($attrSubSet[1], 'expression') !== false) && ($attrSubSet[0]) == 'style') || (strpos($attrSubSet[1], 'javascript:') !== false) || (strpos($attrSubSet[1], 'behaviour:') !== false) || (strpos($attrSubSet[1], 'vbscript:') !== false) || (strpos($attrSubSet[1], 'mocha:') !== false) || (strpos($attrSubSet[1], 'livescript:') !== false));
    }

    /**
     * Try to convert to plaintext
     *
     * @access  protected
     * @param   string  $source
     * @return  string  Plaintext string
     */
    function decode($source)
    {
        // url decode
        $source = html_entity_decode($source, ENT_QUOTES, "ISO-8859-1");
        // convert decimal
        $source = preg_replace('/&#(\d+);/me', "chr(\\1)", $source); // decimal notation
        // convert hex
        $source = preg_replace('/&#x([a-f0-9]+);/mei', "chr(0x\\1)", $source); // hex notation
        return $source;
    }

    /**
     * Method to be called by another php script. Processes for SQL injection
     *
     * @access  public
     * @param   mixed       $source input string/array-of-string to be 'cleaned'
     * @param   resource    $connection - An open MySQL connection
     * @return  string      'cleaned' version of input parameter
     */
    function safeSQL($source, & $connection)
    {
        // clean all elements in this array
        if (is_array($source))
        {
            foreach ($source as $key => $value)
            {
                // filter element for SQL injection
                if (is_string($value))
                {
                    $source[$key] = $this->quoteSmart($this->decode($value), $connection);
                }
            }
            return $source;
            // clean this string
        } else
            if (is_string($source))
            {
                // filter source for SQL injection
                if (is_string($source))
                {
                    return $this->quoteSmart($this->decode($source), $connection);
                }
                // return parameter as given
            } else
            {
                return $source;
            }
    }

    /**
     * Method to escape a string
     *
     * @author  Chris Tobin
     * @author  Daniel Morris
     *
     * @access  protected
     * @param   string      $source
     * @param   resource    $connection     An open MySQL connection
     * @return  string      Escaped string
     */
    function quoteSmart($source, & $connection)
    {
        /*
         * Strip escaping slashes if necessary
         */
        if (get_magic_quotes_gpc())
        {
            $source = stripslashes($source);
        }

        /*
         * Escape numeric and text values
         */
        $source = $this->escapeString($source, $connection);
        return $source;
    }

    /**
     * @author  Chris Tobin
     * @author  Daniel Morris
     *
     * @access  protected
     * @param   string      $source
     * @param   resource    $connection     An open MySQL connection
     * @return  string      Escaped string
     */
    function escapeString($string, & $connection){
        /*
         * Use the appropriate escape string depending upon which version of php
         * you are running
         */
        if (version_compare(phpversion(), '4.3.0', '<')) {
            $string = mysql_escape_string($string);
        } else  {
            $string = mysql_real_escape_string($string);
        }

        return $string;
    }
}

define( "__NOTRIM", 0x0001 );
define( "__ALLOWHTML", 0x0002 );
define( "__ALLOWRAW", 0x0004 );
function getParam( &$arr, $name, $def=null, $mask=0 ) {
    static $noHtmlFilter = null;
    static $safeHtmlFilter = null;

    $return = null;
    if (isset( $arr[$name] )) {
        $return = $arr[$name];

        if (is_string( $return )) {
            // trim data
            if (!($mask&__NOTRIM)) {
                $return = trim( $return );
            }

            if ($mask&__ALLOWRAW) {
                    // do nothing
            } else if ($mask&__ALLOWHTML) {
                    // do nothing - compatibility mode
            } else {
                // send to inputfilter
                if (is_null( $noHtmlFilter )) {
                    $noHtmlFilter = new InputFilter( /* $tags, $attr, $tag_method, $attr_method, $xss_auto */ );
                }
                $return = $noHtmlFilter->process( $return );

                if (!empty($return) && is_numeric($def)) {
                // if value is defined and default value is numeric set variable type to integer
                    $return = intval($return);
                }
            }

            // account for magic quotes setting
            if (!get_magic_quotes_gpc()) {
                $return = addslashes( $return );
            }
        }

        return $return;
    } else {
        return $def;
    }
}

function redirect( $url, $msg='' ) {
    // specific filters
    $iFilter = new InputFilter();
    $url = $iFilter->process($url);
    if (!empty($msg))
        $msg = $iFilter->process($msg);

    // Strip out any line breaks and throw away the rest
    $url = preg_split("/[\r\n]/", $url);
    $url = $url[0];

    if ($iFilter->badAttributeValue( array('href', $url) ))
        $url = $manager->config->sitepath;

    if (trim($msg))
        if (strpos( $url, '?' ))
            $url .= '&msg=' . urlencode($msg);
        else
            $url .= '?msg=' . urlencode($msg);

    if (headers_sent()) {
        echo "\n<script>document.location.href='" . $url . "';</script>\n";
    } else {
        @ob_end_clean(); // clear output buffer
        //header( 'HTTP/1.1 301 Moved Permanently' );
        header("Location: ". $url);
    }
    exit();
}
/**
* Copy the named array content into the object as properties
* only existing properties of object are filled. when undefined in hash, properties wont be deleted
* @param array the input array
* @param obj byref the object to fill of any class
* @param string
* @param boolean
*/
function mosBindArrayToObject( $array, &$obj, $ignore='', $prefix=NULL, $checkSlashes=true ) {
    if (!is_array( $array ) || !is_object( $obj )) {
        return (false);
    }

    $ignore = ' ' . $ignore . ' ';
    foreach (get_object_vars($obj) as $k => $v) {
        if( substr( $k, 0, 1 ) != '_' ) {                        // internal attributes of an object are ignored
            if (strpos( $ignore, ' ' . $k . ' ') === false) {
                if ($prefix) {
                    $ak = $prefix . $k;
                } else {
                    $ak = $k;
                }
                if (isset($array[$ak])) {
                    $obj->$k = ($checkSlashes && get_magic_quotes_gpc()) ? mosStripslashes( $array[$ak] ) : $array[$ak];
                }
            }
        }
    }

    return true;
}
/**
 * Strip slashes from strings or arrays of strings
 * @param mixed The input string or array
 * @return mixed String or array stripped of slashes
 */
function mosStripslashes( &$value ) {
        $ret = '';
        if (is_string( $value )) {
                $ret = stripslashes( $value );
        } else {
                if (is_array( $value )) {
                        $ret = array();
                        foreach ($value as $key => $val) {
                                $ret[$key] = mosStripslashes( $val );
                        }
                } else {
                        $ret = $value;
                }
        }
        return $ret;
}

function makePassword($length=8) {
    $salt            = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $makepass        = '';
    mt_srand(10000000*(double)microtime());
    for ($i = 0; $i < $length; $i++)
        $makepass .= $salt[mt_rand(0,61)];
    return $makepass;
}
function makeDigitPassword($length=8) {
    $salt            = "0123456789";
    $makepass        = '';
    mt_srand(10000000*(double)microtime());
    for ($i = 0; $i < $length; $i++)
        $makepass .= $salt[mt_rand(0,9)];
    return $makepass;
}


function makeHtmlSafe( &$mixed, $quote_style=ENT_QUOTES, $exclude_keys='' ) {
        if (is_object( $mixed )) {
                foreach (get_object_vars( $mixed ) as $k => $v) {
                        if (is_array( $v ) || is_object( $v ) || $v == NULL || substr( $k, 1, 1 ) == '_' ) {
                                continue;
                        }
                        if (is_string( $exclude_keys ) && $k == $exclude_keys) {
                                continue;
                        } else if (is_array( $exclude_keys ) && in_array( $k, $exclude_keys )) {
                                continue;
                        }
                        $mixed->$k = htmlspecialchars( $v, $quote_style );
                }
        }
}

function clearArray(&$arr) {
    if (is_array($arr)) {
        foreach($arr as $k=>$v) {
            if (trim($v)=='') { unset($arr[$k]); }
        }
    }
    return $arr;
}

function p($arr) {
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}

function number($amount, $thousands_sep = " ") {
    return number_format($amount/100, 2, ".", $thousands_sep);
}

/**
 * Verifies that an email is valid.
 *
 * Does not grok i18n domains. Not RFC compliant.
 *
 * @since 0.71
 *
 * @param string $email Email address to verify.
 * @param boolean $deprecated Deprecated.
 * @return string|bool Either false or the valid email address.
 */
function is_email($email) {
    // Test for the minimum length the email can be
    if ( strlen( $email ) < 3 ) {
        //return apply_filters( 'is_email', false, $email, 'email_too_short' );
        return false;
    }

    // Test for an @ character after the first position
    if ( strpos( $email, '@', 1 ) === false ) {
        //return apply_filters( 'is_email', false, $email, 'email_no_at' );
        return false;
    }

    // Split out the local and domain parts
    list( $local, $domain ) = explode( '@', $email, 2 );

    // LOCAL PART
    // Test for invalid characters
    if ( !preg_match( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local ) ) {
        //return apply_filters( 'is_email', false, $email, 'local_invalid_chars' );
        return false;
    }

    // DOMAIN PART
    // Test for sequences of periods
    if ( preg_match( '/\.{2,}/', $domain ) ) {
        //return apply_filters( 'is_email', false, $email, 'domain_period_sequence' );
        return false;
    }

    // Test for leading and trailing periods and whitespace
    if ( trim( $domain, " \t\n\r\0\x0B." ) !== $domain ) {
        //return apply_filters( 'is_email', false, $email, 'domain_period_limits' );
        return false;
    }

    // Split the domain into subs
    $subs = explode( '.', $domain );

    // Assume the domain will have at least two subs
    if ( 2 > count( $subs ) ) {
        //return apply_filters( 'is_email', false, $email, 'domain_no_periods' );
        return false;
    }

    // Loop through each sub
    foreach ( $subs as $sub ) {
        // Test for leading and trailing hyphens and whitespace
        if ( trim( $sub, " \t\n\r\0\x0B-" ) !== $sub ) {
            //return apply_filters( 'is_email', false, $email, 'sub_hyphen_limits' );
            return false;
        }

        // Test for invalid characters
        if ( !preg_match('/^[a-z0-9-]+$/i', $sub ) ) {
            //return apply_filters( 'is_email', false, $email, 'sub_invalid_chars' );
            return false;
        }
    }

    // Congratulations your email made it!
    //return apply_filters( 'is_email', $email, $email, null );
    return true;
}

function sendemail($toemail, $subject, $htmlmessage, $txtmessage)
{
    global $mainframe;
    xload("class.lib.phpmailer");

    $mail = new PHPMailer();
    $mail->SetLanguage("en");
    $mail->CharSet = "utf-8";
    $mail->Hostname = "mailserver";
    $mail->IsSMTP();
    $mail->Host = 'localhost';//$mainframe->config->smtp_host;

    $mail->SetFrom($mainframe->config->email, $mainframe->config->sendername);
    $mail->AddReplyTo($mainframe->config->email, $mainframe->config->sendername);

    $mail->AddAddress($toemail);

    $mail->IsHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $htmlmessage;
    $mail->AltBody = $txtmessage;

    if(!$mail->Send()) {
        unset($mail);
        return false;
    } else {
        unset($mail);
        return true;
    }
}

function send_jabber_msg($to, $message) {
    global $mainframe;

    if (0 == count($to))
        return false;

    xload("class.xmpp.XMPP");

    $xmpp = new XMPPHP_XMPP(
        $mainframe->config->xmpp["host"],
        $mainframe->config->xmpp["port"],
        $mainframe->config->xmpp["user"],
        $mainframe->config->xmpp["password"],
        'xmpphp',
        $mainframe->config->xmpp["server"]
        );

    try {
        $xmpp->connect();
        $xmpp->processUntil('session_start');
        $xmpp->presence();
        for ($i = 0, $countto = count($to); $i < $countto; $i++) {
            $xmpp->message($to[$i], $message);
        }
        $xmpp->disconnect();
    } catch(XMPPHP_Exception $e) {

    }

    return true;
}

function exec_in_bg($cmd) {
    if (substr(php_uname(), 0, 7) == "Windows") {
        pclose(popen("start /B ". $cmd, "r"));
    } else {
        exec($cmd . " >> /home/admbinsecret/logs/tocrm.log &");
    }
}

function rc4($key_str, $data_str) {
    // convert input string(s) to array(s)
    $key = array();
    $data = array();
    for ( $i = 0; $i < strlen($key_str); $i++ ) {
       $key[] = ord($key_str{$i});
    }
    for ( $i = 0; $i < strlen($data_str); $i++ ) {
       $data[] = ord($data_str{$i});
    }
    // prepare key
    $state = array( 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,
                    16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,
                    32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,
                    48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,
                    64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,
                    80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,
                    96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,
                    112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,
                    128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,
                    144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,
                    160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,
                    176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,
                    192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,
                    208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,
                    224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,
                    240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255 );
    $len = count($key);
    $index1 = $index2 = 0;
    for( $counter = 0; $counter < 256; $counter++ ){
       $index2   = ( $key[$index1] + $state[$counter] + $index2 ) % 256;
       $tmp = $state[$counter];
       $state[$counter] = $state[$index2];
       $state[$index2] = $tmp;
       $index1 = ($index1 + 1) % $len;
    }
    // rc4
    $len = count($data);
    $x = $y = 0;
    for ($counter = 0; $counter < $len; $counter++) {
       $x = ($x + 1) % 256;
       $y = ($state[$x] + $y) % 256;
       $tmp = $state[$x];
       $state[$x] = $state[$y];
       $state[$y] = $tmp;
       $data[$counter] ^= $state[($state[$x] + $state[$y]) % 256];
    }
    // convert output back to a string
    $data_str = "";
    for ( $i = 0; $i < $len; $i++ ) {
       $data_str .= chr($data[$i]);
    }

    return $data_str;
}

function RC4_decrypt($hexstr, $key) {
    $data = "";
    for($_loc1 = substr($hexstr, 0, 2) == "0x" ? (2) : (0); $_loc1 < strlen ($hexstr); $_loc1 = $_loc1 + 2) {
        $data .= chr(hexdec(substr($hexstr, $_loc1, 2)));
    }

//    $td = mcrypt_module_open('arcfour', '', 'stream', '');
//    mcrypt_generic_init($td, $key, '');
//
//    $decryptstring = mcrypt_generic($td, $data);
//
//    mcrypt_generic_deinit($td);
//    mcrypt_module_close($td);
//
//    return $decryptstring;
    return rc4($key, $data);
}

function RC4_encrypt($str, $key) {
//    $td = mcrypt_module_open('arcfour', '', 'stream', '');
//    mcrypt_generic_init($td, $key, '');
//
//    $data = bin2hex(mcrypt_generic($td, $str));
//
//    mcrypt_generic_deinit($td);
//    mcrypt_module_close($td);
//    return ($data);
    return bin2hex(rc4($key, $str));
}
?>