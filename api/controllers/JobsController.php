<?php

namespace frontend\modules\api\controllers;

use frontend\components\TActiveController;
use frontend\models\Job;
use frontend\models\JobStatus;
use common\models\Company;
use Yii;
use yii\web\UploadedFile;


class JobsController extends TActiveController
{

    public $modelClass = 'frontend\models\Job';

    /**
     * Index Action not in use currently
     */
    public function actionIndex()
    {
        //Yii::$app->response->format = Response::FORMAT_JSON;
    }


    /**
     * All Jobs for current Tech
     * Return All Jobs for current user or error message
     * @return json Object
     */
    public function actionAllJobs()
    {
        $username = Yii::$app->user->id;
        $company_id = Company::getCompanyBySubdomain()->attributes['id'];
        //$user = User::findByUsername($username, $company_id);

        if (!empty($username)) {
            $query = Job::find()
                ->where(['jobs.tech_id' => $username, 'jobs.company_id' => $company_id])
                ->andWhere("`job_status`.`title` not IN('completed','cancelled') ")
                ->joinWith('company')
                ->joinWith('area')
                ->joinWith('status')
                ->joinWith('type')
                ->orderBy('id')
                ->asArray()
                ->all();

            $jobs = array();
            foreach ($query as $job) {
                $temp = array();
                //$temp['id'] = $job['id'];
                $temp['job_id'] = $job['id'];
                $temp['name'] = $job['first_name'] . $job['last_name'];
                $temp['job_number'] = $job['job_number'];
                $temp['area'] = $job['area']['location'];
                $temp['job_type'] = $job['type']['title'];
                $temp['account_number'] = $job['account_number'];

                array_push($jobs, $temp);
            }
            if (count($jobs) > 0) {
                $data = [
                    "status" => 1,
                    "jobs" => $jobs
                ];
            } else {
                $data = [
                    "status" => 0,
                    "message" => 'No jobs found'
                ];
            }

        } else {
            $data = [
                "status" => 0,
                "message" => "Tech not found",
            ];
        }

        return $data;
    }

    /**
     * Job Details
     * Return Details of a job or error message
     * @return json Object
     */
    public function actionJobDetails()
    {
        $job_id = Yii::$app->request->post('job_id');
        $company_id = Company::getCompanyBySubdomain()->attributes['id'];
        $query = Job::findOne(['id' => $job_id, 'jobs.company_id' => $company_id]);

        if (!empty($query)) {

            $job = [
                'job_id' => $query->id,
                'type' => $query->getTypeName(),
                'location' => $query->getAreaName(),
                'account_number' => $query->account_number,
                'client_name' => $query->first_name . $query->last_name,
                'address' => $query->address,
                'latitude' => $query->latitude,
                'longitude' => $query->longitude,
                'home_phone' => $query->home_phone,
                'other_phone' => $query->other_phone,

            ];

            $data = [
                "status" => 1,
                "job" => $job
            ];
        } else {
            $data = [
                "status" => 0,
                "message" => "job not found",
            ];
        }
        return $data;
    }


    /**
     * Handle Upload
     * Return Status and file path Uploaded
     * @return json Object
     */
    public function actionHandleUpload()
    {
        $file = UploadedFile::getInstanceByName('image');
        if ($file) {
            $extension = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
            if ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg') {
                $randomNumber = rand(0, 999999999);
                $target_name = $randomNumber . "_" . date("Y-m-d") . $file->name;
                if ($file->saveAs(Yii::getAlias('@uploads') . '/jobs/'  . $target_name, true)) {
                    $data = array(
                        "status" => 1,
                        "message" => "Successfully uploaded",
                        "filename" => Yii::getAlias('@uploads') . '/jobs/'  . $target_name
                    );
                } else {
                    $data = array(
                        "status" => 0,
                        "message" => "Upload failed",
                    );
                }
            } else {
                $data = array(
                    "status" => 0,
                    "message" => "Upload failed due to invalid format",
                );
            }
        } else {
            $data = array(
                "status" => 0,
                "message" => "Upload failed",
            );
        }

        return $data;

    }


    /**
     * Close Job
     * Close a job provided in params
     * Return Status with images sent in params
     * @return json Object
     */
    public function actionCloseJob()
    {
        $job_id = Yii::$app->request->post('jobId');
        $company_id = Company::getCompanyBySubdomain()->attributes['id'];
        $lat = Yii::$app->request->post('latitude');
        $long = Yii::$app->request->post('longitude');
        $account_num = Yii::$app->request->post('accountNumber');
        $files = Yii::$app->request->post('files');
        $file_names = explode(',', $files);

        if (!empty($job_id) && (count($file_names) == 3) && !empty($lat) && !empty($long) && !empty($account_num)) {
            $model = Job::findOne(['id' => $job_id, 'jobs.company_id' => $company_id, 'account_number' => $account_num]);
            $status = JobStatus::find()->where(['title' => 'completed'])->one();
            if (!empty($model)) {
                $model->latitude = $lat;
                $model->longitude = $long;
                $model->image1 = $file_names['0'];
                $model->image2 = $file_names['1'];
                $model->image3 = $file_names['2'];
                $model->status_id = $status->id;

                if ($model->save()) {
                    $data = array(
                        "status" => 1,
                        "message" => "Job successfully closed",
                        "images" => array(
                            "image1" => $file_names['0'],
                            "image2" => $file_names['1'],
                            "image3" => $file_names['2'],
                        )

                    );
                } else {
                    $data = array(
                        "status" => 0,
                        "message" => "Job closing unsuccessful",
                    );
                }
            } else {
                $data = array(
                    "status" => 0,
                    "message" => "Job not found",
                );
            }
        } else {
            $data = array(
                "status" => 0,
                "message" => "Required parameters missing",
            );
        }

        return $data;
    }

}
