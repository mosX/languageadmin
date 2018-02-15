<script>
    app.controller('pageCtrl', ['$scope', '$http', function ($scope, $http) {

    }]);
</script>            

<div ng-controller="pageCtrl">
    <div id="page_wrapper">
        <?=$this->m->module('topmenu' . DS . 'tasktable' . DS . 'main') ?>

        <div class="content">
            <script>
                var reservedDates = new Array();
                <?php foreach ($this->m->data as $item) { ?>
                    reservedDates.push(<?= $item ?>);
                <?php } ?>
                $('document').ready(function () {
                    var cal = new Calendar({
                        parent: '#calendar',
                        //startDate:<?= strtotime(date("2016-05-01")) * 1000 ?>,
                        startDate:<?= strtotime(date()) * 1000 ?>,
                        height: '500px',
                        width: '500px',
                        reservedDates: reservedDates
                    });
                });
            </script>

            <div class="container">
                <div id="calendar">
                    <div class="c_box <?= $this->m->_user->id ? 'online' : '' ?>">
                        <div class='header'>
                            <div class="prev_button"></div><div class="current_date">April 2016</div><div class="next_button"></div>
                        </div>
                        <div class='c_dates'>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
