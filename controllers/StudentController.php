<?php

namespace app\controllers;

use app\models\Msg;
use app\models\Pusher;
use app\models\Student;
use app\models\Subject;
use app\models\Teacher;
use Yii;

use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;

class StudentController extends Controller
{
    public $layout = 'studentlayout';

//    public function actions()
//    {
//        return [
//            'captcha' => [
//                'class' => 'yii\captcha\CaptchaAction',
//                'height' => 50,
//                'width' => 80,
//                'minLength' => 4,
//                'maxLength' => 4
//            ],
//        ];
//    }
    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     * 在所有的方法调用前执行
     * 打开session
     * 验证是否登陆
     */
    public function beforeAction($action)
    {
        $session = Yii::$app->session;
        $session->open();
        /**
         * 判断是否存在和他的值,如果为false那么直接去登陆界面
         */
        if ($session['yii']['type'] != 'xs' || $session['yii']['islogin'] != 1) {
            return $this->redirect(Url::toRoute('index/login'));
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    /**
     * @return \yii\web\Response
     * 这个界面就是信息界面
     */
    public function actionIndex()
    {
        return $this->redirect(Url::toRoute('student/msg'));
    }

    /**
     * @return string
     * 信息界面  显示老师和题目
     */
    public function actionMsg()
    {
        $html  = Msg::findOne(['id' => 1]) != null ? Msg::findOne(['id' => 1])->html : '管理员很懒!';
        return $this->render('msg',['html'=>$html]);
    }

    /**
     * @return string
     * 完善个人信息
     */
    public function actionPerson()
    {
        $session = Yii::$app->session;
        $model = Student::findOne(['num' => $session['yii']['num']]);
        if (Yii::$app->request->isPost) {
            $posts = Yii::$app->request->post('Student');
            $model->name = $posts['name'];
            $model->qq = $posts['qq'];
            $model->phone = $posts['phone'];
            $model->pwd = $posts['pwd'];
            $model->save();
            return $this->render('person', ['model' => $model]);
        }
        return $this->render('person', ['model' => $model]);
    }

    /**
     * @return string
     * 选择老师
     */
    public function actionChooseteacher()
    {
        $session = Yii::$app->session;

        $model = Student::findOne(['num' => $session['yii']['num']]);
        $isselect = $model->isselect;
        $ispusher = $model->ispusher;
        $teachers = Teacher::find()->asArray()->all();
        foreach ($teachers as $key => $value) {
            $current = $teachers[$key]['current'];
            $total = $teachers[$key]['total'];
            /**
             * 将这个老师的收学生总数和当前学生人数进行比较
             * 如果满足条件就添加到teacherinfolist中,将要传入到View中
             */
            if ($current < $total) {
                $teacherlist[$teachers[$key]['id']] = $teachers[$key]['name'];
            }
        }
        if (Yii::$app->request->isPost) {
            $model->ispusher = 1;
            $model->save();
            $pusher = new Pusher();
            $pusher->student_id = $model->id;
            $pusher->teacher_id = Yii::$app->request->post('id');
            $pusher->save();
            return $this->render('chooseteacher', ['teacherlist' => $teacherlist, 'isselect' => $isselect, 'ispusher' => 1]);
        }
        return $this->render('chooseteacher', [
            'isselect' => $isselect,
            'ispusher' => $ispusher,
            'teacherlist' => $teacherlist
        ]);
    }

    public function actionSubject()
    {
        $session = Yii::$app->session;
        $subject = Subject::findOne(['student_id' => $session['yii']['id']]);
        if ($subject == null) {
            $subject = new Subject();
        }

        if (Yii::$app->request->isPost) {
            $html = $_POST['html'];
            $content = $_POST['Subject']['content'];
            $title = $_POST['Subject']['title'];
            $subject->title = $title;
            $subject->content = $content;
            $subject->html = $html;
            $subject->student_id = $session['yii']['id'];
            $subject->update_time = date('Y-m-d H:i:s');
            if ($subject->isNewRecord) {
                $subject->insert();
            } else {
                $subject->save();
            }
            return $this->render('subject', ['model' => $subject]);
        }
        return $this->render('subject', ['model' => $subject]);
    }

//    function actionState()
//    {
//        $session = Yii::$app->session;
//        $id = $session['yii']['id'];
//        $model = new Student();
//        $student = $model->findOne(['id' => $id]);
//        $ispusher = $student->ispusher;
//        $isselect = $student->isselect;
//        if ($ispusher != 1) {
//            return $this->redirect(Url::toRoute('student/chooseteacher'));
//        }
//        return $this->render('state', ['ispusher' => $ispusher, 'isselect' => $isselect]);
//
//    }
}
