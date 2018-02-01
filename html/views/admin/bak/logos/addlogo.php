<form action="" method="post" enctype="multipart/form-data" name="upload-image" id="upload-form">
    <input type="hidden" id="MAX_FILE_SIZE" value="134217728" name="MAX_FILE_SIZE">
    <input type="hidden" value="0" name="size" id="imageSize">
    <input type="file" style="font-size: 199px; height: 200px; cursor: pointer; -moz-opacity: 0; opacity: 0; filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=50); margin-left:-450px;" size="1" tabindex="1" maxlength="1024" id="image" name="file">
    <input type="submit" class="authorization-btn" tabindex="2" value="" id="login-button" name="upload">
</form>

    <?php if($this->m->status == 'success'){ ?>
        <script type="text/javascript">
            (function(){
                parent.addSuccess('<?=$this->m->config->assets_url?>/logos/small_<?=$this->m->filename?>',<?=$this->m->logo_id?>);
            })();
        </script>
    <?php }else if($status == 'error'){ ?>
        <script type="text/javascript">
            (function(){
                parent.addError(<?=$this->m->error?>);
            })();
        </script>
    <?php } ?>


<script type="text/javascript">
    /*parent.$('document').ready(function(){
        document.getElementById('imageSize').setAttribute('value',parent.$('select[name=size] option:selected').val());
        parent.$('select[name=size]').change(function(){
            document.getElementById('imageSize').setAttribute('value',parent.$('select[name=size] option:selected').val());
        }); 
    });*/
    
    var changeHandler = function(){
        console.log('change');
        if (document.getElementById('image').value.length > 0) {
            document.forms["upload-image"].submit();
        }
    }
    
    var element = document.getElementById('image');

    if (element.addEventListener) {
        element.addEventListener("change", changeHandler, false);
    } else {
        element.attachEvent("onchange", changeHandler);
    }
</script>