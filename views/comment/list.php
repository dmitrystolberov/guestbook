<?php
if (!empty($user)) {
    ?><a href="/comment/add">Add Comment</a><?
}

if (!empty($list)) {
    foreach ($list as $item) {
        ?><div>
            <div class="row"><?= $item->id?></div>
            <div class="row"><?php
                switch ($item->comment_type_id) {
                    case Comment::TYPE_ID_TEXT:
                        echo htmlspecialchars($item->text,ENT_QUOTES,'utf8');
                        break;
                    case Comment::TYPE_ID_IMAGE:
                        ?><img src="/img/<?= $item->image_id?>"><?
                        break;
                }
                ?></div>
            <div class="row"><?= $item->login?></div>
            <div class="row"><?= $item->created_date?></div><?php
            if (!empty($user) && $user->role_id == User::ROLE_ADMIN) {
                ?><div class="row"><?php
                echo Comment::getStatusTextByStatus($item->status);
                switch ($item->status) {
                    case Comment::STATUS_UNKNOWN:
                        ?><a href="/comment/approve?id<?= $item->id?>"></a>
                        <a href="/comment/reject?id<?= $item->id?>"></a><?
                        break;
                    case Comment::STATUS_APPROVED:
                        ?><a href="/comment/reject?id<?= $item->id?>"></a><?php
                        break;
                    case Comment::STATUS_REJECTED:
                        ?><a href="/comment/approve?id<?= $item->id?>"></a><?php
                        break;
                }
                ?></div><?php
            }
            ?><div class="clear"></div>
        </div><?
    }
}
