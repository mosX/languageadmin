<div id="page_wrapper" style="padding-left:20px; padding-right:20px;">
    <?= $this->m->module('tasks_top_menu') ?>
    <style>
        table{
            background: white;
            width:100%;
            color: #92989b;
            font-size:13px;
        }
        table thead{
            text-align: center;
        }
        table tr{            
            
        }
        table tr td{
            border-bottom: 1px solid #ccc;
            border-right: 1px solid #ccc;
            height:30px;
            position:relative;
            padding:2px 2px 0px 2px;
        }
        table tr:last-child td{

            border-bottom: 1px solid transparent;
        }
        table tr td:first-child{
            text-align: center;
            border-right: 1px solid transparent;
            border-bottom: 1px solid transparent;
        }
        table .timing_label{
            position:relative;
            left:0px;
            top:-14px;
        }
        
        table .item{
            background: #82A855;
            border-radius: 3px;
            border : 1px solid #3a87ad;
            margin-bottom:2px;
            color :white;
            padding:5px;
            font-size:13px;
        }
    </style>
    <table>
        <thead>
            <tr>
                <td style=""></td>
                
                <td style="width:13.5%">ПН</td>
                <td style="width:13.5%">ВТ</td>
                <td style="width:13.5%">СР</td>
                <td style="width:13.5%">ЧТ</td>
                <td style="width:13.5%">ПТ</td>
                <td style="width:13.5%">СБ</td>
                <td style="width:13.5%">ВС</td>
            </tr>
        </thead>
        <tbody>
            <?php $start_date = strtotime(date("Y-m-d 00:00:00")); ?>
            
            <?php for($i=1800,$n=0; $i <= 86400; $i+=1800,$n++ ){ ?>
                <tr>
                    <td>
                        <?php if($n%2){?>
                            <span class="timing_label"><?=date("H:i",$start_date+$i)?></span>
                        <?php } ?>
                    </td>
                    
                    <?php for($j=1;$j<=7;$j++){ ?>
                        <td>                        
                            <?php if($this->m->data[$j][$i]){ ?>
                                <?php foreach($this->m->data[$j][$i] as $item){ ?>
                                    <div class="item">
                                        <div>
                                            Связаться с клиентом
                                        </div>
                                        <div>
                                            <?=$item->fullname?>, <?=$item->comment?>
                                        </div>
                                    </div>                                
                                <?php } ?>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>        
    </table>
</div>

