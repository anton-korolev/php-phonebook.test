<?php

use common\helpers\Pagination;

/** @var array<int,array<string,array{name:string,surname:string}>> $subscriberChunks */
/** @var int $pageCount */
/** @var int $currentPage */

?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-6">Список абонентов:</h1>
    </div>

    <div class="body-content">

        <div class="row justify-content-md-center">
            <?
            foreach ($subscriberChunks as $subscribers) {
                echo <<<'END'
            <div class="col-md-auto">
            END;

                foreach ($subscribers as $phone => $subscriber) {
                    $subscriber['surname'] = htmlspecialchars($subscriber['surname']);
                    $subscriber['name'] = htmlspecialchars($subscriber['name']);
                    echo <<<"END"
                    <div><a href="/subscriber/view?phone=$phone"><b>$phone</b> - {$subscriber['surname']} {$subscriber['name']}</a></div>
                END;
                }

                echo <<<'END'
            </div>
            END;
            }
            ?>
        </div>

    </div>

    <div class="text-center bg-transparent">&nbsp;</div>

    <div class="text-center bg-transparent">
        <?= Pagination::render($pageCount, $currentPage, 10) ?>
    </div>

</div>
