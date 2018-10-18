<?php
if (!empty($user)) {
    ?><a class="btn btn-default" role="button" href="/comment/add">Add Comment</a><?
}
$isAdmin = (!empty($user) && User::ROLE_ADMIN == (int)$user->role);

if (!empty($list)) {
    ?><table class="table">
        <thead>
            <tr>
                <th>Comment</th>
                <th>User</th>
                <th>Created date</th><?php
                if ($isAdmin) {
                    ?><th>Status</th>
                    <th>Actions</th><?php
                }
            ?></tr>
        </thead>
        <tbody>
            <?php
            foreach ($list as $item) {
                ?><tr>
                    <td><?php
                        switch ($item->comment_type_id) {
                            case Comment::TYPE_ID_TEXT:
                                echo htmlspecialchars($item->text,ENT_QUOTES,'UTF-8');
                                break;
                            case Comment::TYPE_ID_IMAGE:
                                ?><img height="50px" src="/img/<?= $item->image_name?>"><?
                                break;
                        }
                        ?></td>
                    <td><?= $item->login?></td>
                    <td><?= $item->created_date?></td><?php
                    if ($isAdmin) {
                        ?><td><?= Comment::getStatusTextByStatus($item->status)?></td>
                        <td>
                            <a class="btn btn-default" role="button" href="/comment/edit?id=<?= $item->id?>">Edit</a><?php
                                switch ((int)$item->status) {
                                    case Comment::STATUS_NEW:
                                        ?><a class="btn btn-success" role="button" href="/comment/approve?id=<?= $item->id?>">Approve</a>
                                        <a class="btn btn-danger" role="button" href="/comment/reject?id=<?= $item->id?>">Reject</a><?
                                        break;
                                    case Comment::STATUS_APPROVED:
                                        ?><a class="btn btn-danger" role="button" href="/comment/reject?id=<?= $item->id?>">Reject</a><?php
                                        break;
                                    case Comment::STATUS_REJECTED:
                                        ?><a class="btn btn-success" role="button" href="/comment/approve?id=<?= $item->id?>">Approve</a><?php
                                        break;
                                }
                            ?><a class="btn btn-danger" role="button" href="/comment/delete?id=<?= $item->id?>">Delete</a>
                        </td><?php
                    }
                ?></tr><?php
            }
        ?></tbody>
    </table><?php
}
