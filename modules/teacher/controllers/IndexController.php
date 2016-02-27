<?php

namespace app\modules\teacher\controllers;

use app\models\Pusher;
use app\models\Relative;
use app\models\Student;
use app\models\Teacher;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;

class IndexController extends Controller
{
    public $layout = "teacherlayout";

    public function beforeAction($action)
    {
        /**
         *  通过session进行登录验证
         *  如果action的getUniqueId是'su/index/login'
         *  防止循环重定向,进行一个判断
         */
        if ($action->getUniqueId() !== 'teacher/index/login') {
            $session = \Yii::$app->session;
            $session->open();
            if ($session['yii']['type'] != 't' || $session['yii']['islogin'] != 1) {
                return $this->redirect(Url::toRoute('index/login'));
            } else {
                return parent::beforeAction($action);
            }
        }


        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionLogin()
    {
        $this->layout = 'main';
        if (\Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model = Teacher::findOne($post['Teacher']);
            if (empty($model)) {
                $model = new Teacher();
                $model->num = $post['Teacher']['num'];
                return $this->render('login', ['model' => $model]);
            }
            $session = \Yii::$app->session;
            $session->open();
            $session->remove('yii');
            $session['yii'] = [
                'type' => 't',
                'islogin' => 1,
                'id' => $model->id,
                'num' => $model->num,
                'name' => $model->name
            ];
            return $this->redirect(Url::toRoute('index/index'));
        } else {
            $model = new Teacher();
            return $this->render('login', ['model' => $model]);
        }
    }

    /**
     *老师信息编辑
     */
    public function actionEdit()
    {
        $session = Yii::$app->session;
        $id = $session['yii']['id'];
        $model = Teacher::findOne(['id' => $id]);
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model->num = $post['Teacher']['num'];
            $model->pwd = $post['Teacher']['pwd'];
            $model->name = $post['Teacher']['name'];
            $model->phonenum = $post['Teacher']['phonenum'];
            $model->qq = $post['Teacher']['qq'];
            $model->email = $post['Teacher']['email'];
            $model->qqgroup = $post['Teacher']['qqgroup'];
            $model->total = $post['Teacher']['total'];
            $model->save();
            return $this->render('edit', ['model' => $model]);
        }

        return $this->render('edit', ['model' => $model]);
    }

    /**
     * 老师学生关系
     */
    public function actionRelative()
    {
        $session = Yii::$app->session;
        $dataProvider = new ActiveDataProvider([
            'query' => Relative::find()->where(['teacher_id' => $session['yii']['id']])->orderBy(['id' => 'DESC']),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('relative', ['dataProvider' => $dataProvider]);
    }

    public function actionPusher()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Pusher::find()->orderBy(['id' => 'DESC']),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('pusher', ['dataProvider' => $dataProvider]);
    }

    public function actionRefuse($stuid, $pusherid)
    {
        $pusher = Pusher::findOne(['id' => $pusherid]);
        $student = $pusher->student;
        $student->ispusher = null;
        $student->isselect = null;
        $pusher->delete();
        $student->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param $stuid
     * @param $pusherid
     * @return \yii\web\Response
     * student->isselect
     * relative->insert
     * pusher->delete
     * teacher->current++
     */
    public function actionReceive($stuid, $pusherid)
    {
        $session = Yii::$app->session;
        Pusher::deleteAll(['id' => $pusherid]);
        $student = Student::findOne(['id' => $stuid]);
        $student->isselect = 1;
        $student->save();

        $relative = new Relative();
        $relative->student_id = $stuid;
        $relative->teacher_id = $session['yii']['id'];
        $relative->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDeleterelative($stuid)
    {
        $student = Student::findOne(['id'=>$stuid]);
        $student->ispusher = null;
        $student->isselect = null;
        $student->save();
        $relative = $student->relative;
        $relative->delete();
        return $this->redirect(Yii::$app->request->referrer);
    }

}
