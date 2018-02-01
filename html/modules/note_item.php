<div class='note_item_wrapper' data-id="<?=$this->note->id?>">
    <div class='icon'>                    
        <svg class=""><use xlink:href="#notes--feed-note"></use></svg>
    </div>
    <div class='content'>
        <div class="header" data-time="<?=date("Y-m-d H:i:s",strtotime($this->note->date))?>">Сегодня <?=date("H:i",strtotime($this->note->date))?> <?=$this->note->parent?></div>
        <div class="message"><?=strip_tags(trim($this->note->message))?></div>
    </div>

    <div class="actions_block">
        <a class="pin" tabindex="-1" href=""><svg style="width:16px;height:15px;fill:transparent; stroke:#feaa18"><use xlink:href="#notes--pin"></use></svg> Закрепить</a>
        <a class="del" tabindex="-1" href=""><svg style="width:14px;height:14px; fill:#f86161"><use xlink:href="#notes--context-delete"></use></svg> Удалить</a>
        <a class="edit" tabindex="-1" href=""><svg style="width:9.58px;height:13.813px; fill:#a3acba"><use xlink:href="#notes--context-edit"></use></svg>Изменить</a>
    </div>
</div>