<?php

class CommentController extends BaseController
{

    public function actionList()
    {
        $user = Session::getInstance()->getUser();
        $status = (empty($user) || (int)$user->role == User::ROLE_USER) ? Comment::STATUS_APPROVED : null;
        $list = Comment::model()->findAll($status);
        $this->render('list', compact('list', 'user'));
    }

    public function actionDelete()
    {
        $this->checkAccess();
        $commentId = Request::getInstance()->getParam('id');
        $comment = Comment::model()->findByAttributes(['id' => $commentId]);
        if ($comment) {
            if ($comment->comment_type_id == Comment::TYPE_ID_IMAGE) {
                $image = Image::model()->findByAttributes(['id' => $comment->image_id]);
                if ($image) {
                    unlink('img/' . $image->image_name);
                }
                $image->delete();
            }
            $comment->delete();
        }
        Request::getInstance()->redirect('/');
    }

    public function actionEdit()
    {
        $this->checkAccess();
        $commentId = Request::getInstance()->getParam('id');
        $comment = Comment::model()->findByAttributes(['id' => $commentId]);
        $image = $oldImageName = null;
        if ($comment && $comment->comment_type_id == Comment::TYPE_ID_IMAGE) {
            $image = Image::model()->findByAttributes(['id' => $comment->image_id]);
            if ($image) {
                $oldImageName = $image->image_name;
            }
        }
        if (Request::getInstance()->getIsPostRequest()) {
            $commentId = $this->saveComment($comment, $oldImageName);
            if ($commentId) {
                Request::getInstance()->redirect('/');
            }
        }
        $this->render('edit', compact('comment', 'image'));
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
            $comment = new Comment();
            $comment->user_id = $user->id;
            $commentId = $this->saveComment($comment);
            if ($commentId) {
                Request::getInstance()->redirect('/');
            }
        }
        $this->render('add');
    }

    /**
     * @param $comment
     * @param null $oldImageName
     * @return int
     */
    private function saveComment($comment, $oldImageName = null)
    {
        $request = Request::getInstance();
        $text = $request->getParam('text');
        if (!empty($text)) {
            $comment->text = $text;
            $comment->comment_type_id = Comment::TYPE_ID_TEXT;
            $comment->image_id = null;
            if ($oldImageName) {
                unlink('img/' . $oldImageName);
            }
        } elseif (!empty($_FILES)) {
            $comment->text = null;
            $imageName = $this->uploadImage($oldImageName);
            if ($imageName) {
                $image = new Image();
                $image->setAttributes([
                    'image_name' => $imageName
                ]);
                $imageId = $image->save();
                if (!empty($imageId)) {
                    $comment->comment_type_id = Comment::TYPE_ID_IMAGE;
                    $comment->image_id = $imageId;
                }
            }
        }
        return $comment->save();
    }

    /**
     * @param null $oldImageName
     * @return string
     */
    private function uploadImage($oldImageName = null)
    {
        if (!empty($_FILES)) {
            if ($_FILES['file']['error'] == 0) {
                $extsAllowed = array('jpg', 'jpeg', 'png', 'gif');
                $extUpload = strtolower(substr(strrchr($_FILES['file']['name'], '.'), 1));
                $tmpNameParts = explode('/', $_FILES['file']['tmp_name']);
                $imageName = end($tmpNameParts) . '.' . $extUpload;
                if (in_array($extUpload, $extsAllowed)) {
                    $target = 'img/' . $imageName;
                    $result = move_uploaded_file($_FILES['file']['tmp_name'], $target);

                    if ($result) {
                        if ($oldImageName) {
                            unlink('img/' . $oldImageName);
                        }
                        return $imageName;
                    }
                }
            }

        }
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
        Request::getInstance()->redirect('/');
    }

    private function checkAccess()
    {
        $user = Session::getInstance()->getUser();
        if (empty($user) || (int)$user->role == User::ROLE_USER) {
            Request::getInstance()->redirect('/');
        }
    }
}
