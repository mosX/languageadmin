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
        table thead tr td{
            height: 40px;
            border-bottom:1px solid #ccc;
        }
        table tr{
            
        }
        table tr td{            
            text-align: center;
            border-bottom: 1px solid #ccc;
            border-right: 1px solid #ccc;
            height:30px;
            position:relative;
            padding:2px 2px 0px 2px;
            height: 200px;
        }
        table tbody tr:last-child td{
            border-bottom: 1px solid transparent;   
        }
        table tr td:first-child{
            
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
        table .date_number{
            position:absolute;
            
            top:10px; 
            right: 10px;
        }
    </style>
    <table>
        <thead>
            <tr>
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
            
            <?php for($w=0; $w <= 5; $w++){ ?>
                <tr>                                       
                    <?php for($j=1;$j<=7;$j++,$this->m->start+=86400){ ?>
                        <td>                        
                            <span class="date_number"><?=date("d",$this->m->start)?></span>                            
                            
                            
                                <?php if($this->m->data[date("Y-m-d",$this->m->start)]){ ?>
                                
                                    <?php foreach($this->m->data[date("Y-m-d",$this->m->start)] as $item){ ?>
                                        <div class="item">
                                            <div>Связаться с клиентом</div>
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

