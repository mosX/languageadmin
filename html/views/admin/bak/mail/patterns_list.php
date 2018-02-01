

    <?php foreach($this->m->data as $item){ ?>
        <div class='item' data_id='<?=$item->id?>'>
            <div class='edit_btn'></div>
            <?=$item->name?>
        </div>
    <?php } ?>

<script>
    $('document').ready(function(){
        $('.add_pattern').click(function(){
            $('#addPatternsModal').modal('show');
            return false;
        });
    });
</script>
<button class='add_pattern'><span></span>Добавить Шаблон</button>