<?php

class CommentController extends BaseController
{


    public function actionList()
    {
        $user = Session::getInstance()->getUser();
        $status = (!empty($user) && $user->role_id == User::ROLE_USER) ? Comment::STATUS_APPROVED : null;
        $list = Comment::model()->findAll($status);
        $this->render('list', compact('list', 'user'));
    }

    public function actionAdd()
    {
        $request = Request::getInstance();
        $isGuest = Session::getInstance()->isGuest();
        $user = Session::getInstance()->getUser();
        if ($isGuest) {
            $request->redirect('/');
        }
        if ($request->getIsPostRequest()) {

            $text = $request->getParam('text');
            $comment = new Comment();
            $comment->user_id = $user->id;
            if (!empty($text)) {
                $comment->text = $text;
                $comment->comment_type_id = Comment::TYPE_ID_TEXT;
            } elseif (!empty($_FILES)) {
                if ($_FILES['file']['error'] == 0) {
                    $extsAllowed = array('jpg', 'jpeg', 'png', 'gif');
                    $extUpload = strtolower(substr(strrchr($_FILES['file']['name'], '.'), 1));
                    if (in_array($extUpload, $extsAllowed)) {
                        $name = "img/{$_FILES['file']['name']}";
                        $result = move_uploaded_file($_FILES['file']['tmp_name'], $name);

                        if ($result) {
                            $image = new Image();
                            $image->setAttributes([
                                'image_name' => $name
                            ]);
                            $imageId = $image->save();
                            if (!empty($imageId)) {
                                $comment->comment_type_id = Comment::TYPE_ID_IMAGE;
                                $comment->image_id = $imageId;
                            } else {
                                unlink($name);
                            }
                        }
                    } else {
                        echo 'File is not valid. Please try again';
                    }
                }

            } else {
                //error
            }
            $commentId = Comment::model()->insert();
            if ($commentId) {
                Request::getInstance()->redirect('/comment/list');
            }
        }
        $this->render('add');
    }

    public function actionReject()
    {
        $this->changeStatus(Comment::STATUS_REJECTED);
    }

    public function actionApprove()
    {
        $this->changeStatus(Comment::STATUS_APPROVED);
    }

    /**
     * @param $newStatus
     */
    private function changeStatus($newStatus)
    {
        $this->checkAccess();
        $commentId = (int)Request::getInstance()->getParam('id');
        if (!empty($commentId)) {
            $model = Comment::model()->findByAttributes(['id' => $commentId]);
            if ($model) {
                $model->status = $newStatus;
                $model->save();
            }
        }
        Request::getInstance()->redirect('/comment/list');
    }

    private function checkAccess()
    {
        $user = Session::getInstance()->getUser();
        if (empty($user) || $user->role_id == User::ROLE_USER) {
            Request::getInstance()->redirect('/');
        }
    }
}
