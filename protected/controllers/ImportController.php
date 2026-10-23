<?php

class ImportController extends Controller
{
	public function actionPlotData()
	{
		$uploadFolder = getcwd() . '/imports/plots/';
        $fileName = 'report.csv';
        $orig_fileName = $_FILES['plot']['name'];
        move_uploaded_file($_FILES['plot']['tmp_name'], $uploadFolder.$fileName);
        $result = [];
        if (($handle = fopen($uploadFolder.$fileName, 'r')) !== FALSE) {
            $index = 0;
            while (($row = fgetcsv($handle, 100000, ',')) !== FALSE) {

                if (empty($header)) {
                    $header = $row;
                } else {
                    $result[] = $row;
                }
                $index++;
            }
            fclose($handle);
        }

        //$sql = 'SET FOREIGN_KEY_CHECKS = 0;TRUNCATE `plots`; SET FOREIGN_KEY_CHECKS = 1;';
        //Yii::app()->db->createCommand($sql)->execute();
        
        if(!empty($result)){
            //unset($result[0]);
            foreach ($result as $i=>$value) {
            	//if(!empty($value[0])){
            		//plot
            		//echo '<pre>';
                    //print_r($result);
                   	//exit;
                   	if($value[2]=='200 S.Y'){
                   	    $size = 4;
                   	}
                   	
                   	if($value[2]=='120 S.Y'){
                   	    $size = 2;
                   	}
                   	
                   	if($value[2]=='80 S.Y'){
                   	    $size = 1;
                   	}
                   	
                   	if($value[2]=='150 S.Y'){
                   	    $size = 3;
                   	}
		            $plot = new Plots;
					$plot->block_number = $value[0];
					$plot->category_id = ($value[3]=='RESIDENTIAL')?1:2;
					$plot->size_id = $size;
					$plot->plot_number = $value[1];
					$plot->length = 0;
					$plot->width = 0;
					$plot->description = 0;
					$plot->plot_type = 0;
					$plot->is_road_facing = 0;
					$plot->is_road_facing_amount = 0;
					$plot->is_corner = 0;
					$plot->is_corner_amount = 0;
					$plot->is_park_facing = 0;
					$plot->is_park_facing_amount = 0;
					$plot->is_west_open = 0;
					$plot->is_west_open_amount = 0;
					$plot->total = 0;
					$plot->discount = 0;
					$plot->phase_id = 1;
					$plot->status = 0;
		            $plot->save(false);
            	//}
            }
        }
        echo 'Done';
	}

	public function actionUpload()
	{

		$this->render('upload');
	}

	public function actionAgentupdate()
	{

		$data['action'] = Yii::app()->baseUrl.'/import/dealerdataupdated';
		$this->render('others',$data);
	}

	public function actionPlotupdate()
	{
		$data['action'] = Yii::app()->baseUrl.'/import/plotupdated';
		$this->render('others',$data);
	}

	public function actionUploaddealer()
	{

		$this->render('uploadDealer');
	}


	public function actionDealerData()
	{
		$uploadFolder = getcwd() . '/imports/dealers/';
        $fileName = 'report.csv';
        $orig_fileName = $_FILES['plot']['name'];
        move_uploaded_file($_FILES['plot']['tmp_name'], $uploadFolder.$fileName);
        $result = [];
        if (($handle = fopen($uploadFolder.$fileName, 'r')) !== FALSE) {
            $index = 0;
            while (($row = fgetcsv($handle, 100000, ',')) !== FALSE) {

                if (empty($header)) {
                    $header = $row;
                } else {
                    $result[] = $row;
                }
                $index++;
            }
            fclose($handle);
        }
        //echo '<pre>';print_r($result);exit;
        //$sql = 'SET FOREIGN_KEY_CHECKS = 0;TRUNCATE `agents`;TRUNCATE `agent_plots`;SET FOREIGN_KEY_CHECKS = 1;';
        //Yii::app()->db->createCommand($sql)->execute();
        //unset($result[0]);
            foreach ($result as $ind => $value) {
          		if(!empty($result)){
		            //
            	/*if(!empty($value[0])){
            		
            		$agentParent = Agents::model()->find('name = :name AND parent_id IS NULL',array(':name'=>$value[9]));

					//$ps = PaymentSchedules::model()->find('name = :name',array(':name'=>$value[7]));

            		$plot = Plots::model()->find('plot_number = :plt AND block_number = :blk AND plot_type = :pt',array(
            			':blk'=>$value[1],
            			':pt'=>$value[2],
            			':plt'=> $value[3],//sprintf('%03d', $value[3]),
            		));
            		
            		//$discount = str_replace('PKR ','',str_replace(',', '' , $value[10]));

            		
            		// echo '<pre>';
            		// print_r($value);
            		// print_r(@$agentParent->attribute);
            		// print_r($value);
            		// exit;
            		if($plot){
            			//agent
						if($agentParent){
							if($value[10]){
								$parentIDD = $agentParent->id;
								$subAgent = Agents::model()->find('name = :name AND parent_id = :parent',array(':parent'=>$parentIDD,':name'=>$value[10]));
								if(!$subAgent){
									$subAgent = new Agents;
									$subAgent->name = $value[9];
									$subAgent->parent_id = $agentParent->id;
									$subAgent->percentage = 0;//$value[11];//str_replace('%','',$value[12]);
									$subAgent->percentage_value = 0;//$value[12];
									$subAgent->number = '0';
									$subAgent->phase_id = 1;
									$subAgent->status = 1;
									$subAgent->save(false);
								}
							} else{
								$parentIDD = $agentParent->id;
								$subAgent = Agents::model()->find('name = :name AND parent_id = :parent',array(':parent'=>$parentIDD,':name'=>$value[10]));
								//$subAgent = (object)$subAgent->attributes;	
							}


							$agentPlot = AgentPlots::model()->find('plot_id = :id',array(':id'=>$plot->id));
							if($agentPlot){
								$agentPlot->delete();
							}
							$ap = new AgentPlots;
							$ap->agent_id = @$subAgent->id;
							$ap->plot_id = $plot->id;
							// if($ps){
							$ap->payment_schedule_id = 0;//$ps->id;	
							// }
							
							$ap->discount = 0;//$discount;
							//if($value[12]){
							$ap->commission = 0;//$value[12];
							//}
							$ap->createdOn = date('Y-m-d');
							$ap->save(false);


							if($plot->customerPlots){
								foreach($plot->customerPlots as $cp){
									$cp->is_agent = 1;
									$cp->agent_id = $subAgent->id;
									$cp->agent_percentage = 0;//@$value[12];
									$cp->save(false);

									//Booking Expense
									/*if($value[18]>0){
										$expModel = new Expenses;
										$expModel->expense_type = 10;
										$expModel->account_id = 5;
										$expModel->description = 'Booking agent commission for Plot *'.($cp->plot->block_number.'-'.$cp->plot->plot_type.'-'.$cp->plot->plot_number).'*';
										$expModel->amount = $value[18];
										$expModel->user_id = $a->id;
										$expModel->status = 1;
										$expModel->reason = NULL;
										$expModel->number = $value[15];
										$ddate = explode('-', $value[16]);
										$expModel->createdOn = '20'.$ddate[2].'-'.$ddate[1].'-'.$ddate[0];
										$expModel->phase_id = 1;
										$expModel->booking_id = $cp->id;
										$expModel->payment_mode = $value[13];
										$expModel->paid_to = 'Agent '.$a->name;
										$expModel->bank = $value[14];
										$expModel->cnic = $value[15];
										$expModel->save(false);
									}
									
								}
							}
							
						} else{

							//Agent
							$agentParent = Agents::model()->find('name = :name AND parent_id IS NULL',array(':name'=>$value[9]));
							if($value[10]){
								//parent
								$pa = new Agents;
								$pa->name = $value[9];
								$pa->parent_id = NULL;
								$pa->percentage = 0;//$value[11];//str_replace('%','',$value[12]);
								$pa->percentage_value = 0;
								$pa->number = '0';
								$pa->phase_id = 1;
								$pa->status = 1;
								$pa->save(false);	
								//subdealer
								$a = new Agents;
								$a->name = $value[10];
								$a->parent_id = $pa->id;
								$a->percentage = 0;//str_replace('%','',$value[12]);
								$a->percentage_value = 0;//$value[12];
								$a->number = '0';
								$a->phase_id = 1;
								$a->status = 1;
								$a->save(false);
							}
							

							$agentPlot = AgentPlots::model()->find('plot_id = :id',array(':id'=>$plot->id));
							if($agentPlot){
								$agentPlot->delete();
							}
							//agent Plot
							$ap = new AgentPlots;
							$ap->agent_id = $a->id;
							$ap->plot_id = $plot->id;
							// if($ps){
							$ap->payment_schedule_id = 0;//$ps->id;	
							// }
							//if($value[12]){
							$ap->commission = 0;//$value[12];
							//}
							$ap->discount = 0;//$discount;
							$ap->createdOn = date('Y-m-d');
							$ap->save(false);

							if($plot->customerPlots){
								foreach($plot->customerPlots as $cp){
									$cp->is_agent = 1;
									$cp->agent_id = $a->id;
									$cp->agent_percentage = 0;//@$value[12];
									$cp->save(false);

									//Booking Expense
									/*if($value[18]>0){
										$expModel = new Expenses;
										$expModel->expense_type = 10;
										$expModel->account_id = 5;
										$expModel->description = 'Booking agent commission for Plot *'.($cp->plot->block_number.'-'.$cp->plot->plot_type.'-'.$cp->plot->plot_number).'*';
										$expModel->amount = $value[18];
										$expModel->user_id = $a->id;
										$expModel->status = 1;
										$expModel->reason = NULL;
										$expModel->number = $value[15];
										$ddate = explode('-', $value[16]);
										$expModel->createdOn = $ddate[2].'-'.$ddate[1].'-'.$ddate[0];
										$expModel->phase_id = 1;
										$expModel->booking_id = $cp->id;
										$expModel->payment_mode = $value[13];
										$expModel->paid_to = 'Agent '.$a->name;
										$expModel->bank = $value[14];
										$expModel->cnic = $value[15];
										$expModel->save(false);
									}
								}
							}
						}

						//$plot->discount = $discount;
						// if($ps){
						// 	$plot->total = $this->getPaymentScheduleTotal($plot->plot_type,$ps->id);	
						// }
						
						//$plot->save(false);
            		}

            	}*/

            	//Plot
            	$plot = Plots::model()->find('plot_number = :plt AND block_number = :blk AND plot_type = :pt',array(
        			':blk'=>$value[1],
        			':pt'=>$value[2],
        			':plt'=> $value[3],//sprintf('%03d', $value[3]),
        		));
            	//Agent
				$agentParent = Agents::model()->find('name = :name AND parent_id IS NULL',array(':name'=>$value[9]));
				if($agentParent){
					$parentIDD = $agentParent->id;
					$subAgent = Agents::model()->find('name = :name AND parent_id = :parent',array(':parent'=>$parentIDD,':name'=>$value[10]));
					if(!$subAgent){
						$subAgent = new Agents;
						$subAgent->name = $value[10];
						$subAgent->parent_id = $agentParent->id;
						$subAgent->percentage = 0;//$value[11];//str_replace('%','',$value[12]);
						$subAgent->percentage_value = 0;//$value[12];
						$subAgent->number = '0';
						$subAgent->phase_id = 1;
						$subAgent->status = 1;
						$subAgent->save(false);
						$agent = $subAgent->id;
					} else{
						$agent = $subAgent->id;
					}
					

					$agentPlot = AgentPlots::model()->find('plot_id = :id',array(':id'=>$plot->id));
					if($agentPlot){
						$agentPlot->delete();
					}
					//agent Plot
					$ap = new AgentPlots;
					$ap->agent_id = $agent;
					$ap->plot_id = $plot->id;
					$ap->payment_schedule_id = 0;//$ps->id;	
					$ap->commission = 0;//$value[12];
					$ap->discount = 0;//$discount;
					$ap->createdOn = date('Y-m-d');
					$ap->save(false);

					if($plot->customerPlotsRand){
						foreach($plot->customerPlotsRand as $cp){
							$cp->is_agent = 1;
							$cp->agent_id = $agent;
							$cp->agent_percentage = 0;//@$value[12];
							$cp->save(false);
						}
					}
				} else{
					//parent
					$pa = new Agents;
					$pa->name = $value[9];
					$pa->parent_id = NULL;
					$pa->percentage = 0;//$value[11];//str_replace('%','',$value[12]);
					$pa->percentage_value = 0;
					$pa->number = '0';
					$pa->phase_id = 1;
					$pa->status = 1;
					$pa->save(false);	
					//subdealer
					$a = new Agents;
					$a->name = $value[10];
					$a->parent_id = $pa->id;
					$a->percentage = 0;//str_replace('%','',$value[12]);
					$a->percentage_value = 0;//$value[12];
					$a->number = '0';
					$a->phase_id = 1;
					$a->status = 1;
					$a->save(false);

					//add agent plot
					$agentPlot = AgentPlots::model()->find('plot_id = :id',array(':id'=>$plot->id));
					if($agentPlot){
						$agentPlot->delete();
					}
					//agent Plot
					$ap = new AgentPlots;
					$ap->agent_id = $a->id;
					$ap->plot_id = $plot->id;
					$ap->payment_schedule_id = 0;//$ps->id;	
					$ap->commission = 0;//$value[12];
					$ap->discount = 0;//$discount;
					$ap->createdOn = date('Y-m-d');
					$ap->save(false);

					if($plot->customerPlotsRand){
						foreach($plot->customerPlotsRand as $cp){
							$cp->is_agent = 1;
							$cp->agent_id = $a->id;
							$cp->agent_percentage = 0;//@$value[12];
							$cp->save(false);
						}
					}
				}
            }
        }

        echo 'Done';
	}


	public function actionDealerDataUpdated()
	{
		$uploadFolder = getcwd() . '/imports/dealers/';
        $fileName = 'report.csv';
        $orig_fileName = $_FILES['plot']['name'];
        move_uploaded_file($_FILES['plot']['tmp_name'], $uploadFolder.$fileName);
        $result = [];
        if (($handle = fopen($uploadFolder.$fileName, 'r')) !== FALSE) {
            $index = 0;
            while (($row = fgetcsv($handle, 100000, ',')) !== FALSE) {

                if (empty($header)) {
                    $header = $row;
                } else {
                    $result[] = $row;
                }
                $index++;
            }
            fclose($handle);
        }

    //$sql = 'SET FOREIGN_KEY_CHECKS = 0;TRUNCATE `agent_plots`;SET FOREIGN_KEY_CHECKS = 1;';
    //Yii::app()->db->createCommand($sql)->execute();
    print_r($result);exit;
    	foreach($result as $index=>$row){
       		$commission = str_replace('PKR ','',str_replace(',','',$row[8]));
       		$discount = str_replace('PKR ','',str_replace(',','',$row[7]));
       		$plot = Plots::model()->find('block_number = :block AND plot_type = :type AND plot_number = :number',array(':block'=>$row[1],':type'=>$row[2],':number'=>$row[3]));
       		$ps = PaymentSchedules::model()->find('name = :name',array(':name'=>$row[4]));
       		if($plot){
       			$agent = Agents::model()->find('name = :name AND parent_id IS NULL',array(':name'=>$row[5]));
       			if(!$agent){
	       			$agent = new Agents;
					$agent->name = $row[5];
					$agent->parent_id = NULL;
					$agent->percentage = 0;//str_replace('%','',$value[12]);
					$agent->percentage_value = 0;
					$agent->number = '0';
					$agent->phase_id = 1;
					$agent->status = 1;
					$agent->save(false);
				}
       			$subAgent = Agents::model()->find('name = :name AND parent_id = :parent',array(':parent'=>$agent->id,':name'=>$row[6]));
       			if(!$subAgent){
					$subAgent = new Agents;
					$subAgent->name = $row[6];
					$subAgent->parent_id = $agent->id;
					$subAgent->percentage = 0;//$value[11];//str_replace('%','',$value[12]);
					$subAgent->percentage_value = 0;//$commission;
					$subAgent->number = '0';
					$subAgent->phase_id = 1;
					$subAgent->status = 1;
					$subAgent->save(false);
				}
       			if($plot->customerPlots){

       				$cp = CustomerPlots::model()->findByPk($plot->customerPlots[0]->id);
       				$cp->agent_id = $subAgent->id;
       				$cp->agent_percentage = $commission;
       				$cp->save(false);

       				//agent Plot
					$ap = new AgentPlots;
					$ap->agent_id = $subAgent->id;
					$ap->plot_id = $plot->id;
					if($ps){
						$ap->payment_schedule_id = $ps->id;	
					}
					if($commission){
						$ap->commission = $commission;
					}
					$ap->discount = $row[7];
					$ap->createdOn = date('Y-m-d');
					$ap->save(false);

       			} else{
       				//agent Plot
					$ap = new AgentPlots;
					$ap->agent_id = $subAgent->id;
					$ap->plot_id = $plot->id;
					if($ps){
						$ap->payment_schedule_id = $ps->id;	
					}
					if($commission){
						$ap->commission = $commission;
					}
					$ap->discount = $discount;
					$ap->createdOn = date('Y-m-d');
					$ap->save(false);
       			}
       		}
       }
       echo 'Done';
    }

    public function actionPlotUpdated()
	{
		$uploadFolder = getcwd() . '/imports/dealers/';
        $fileName = 'report.csv';
        $orig_fileName = $_FILES['plot']['name'];
        move_uploaded_file($_FILES['plot']['tmp_name'], $uploadFolder.$fileName);
        $result = [];
        if (($handle = fopen($uploadFolder.$fileName, 'r')) !== FALSE) {
            $index = 0;
            while (($row = fgetcsv($handle, 100000, ',')) !== FALSE) {

                if (empty($header)) {
                    $header = $row;
                } else {
                    $result[] = $row;
                }
                $index++;
            }
            fclose($handle);
        }

    $sql = 'SET FOREIGN_KEY_CHECKS = 0;TRUNCATE `plot_boundries`;SET FOREIGN_KEY_CHECKS = 1;';
    Yii::app()->db->createCommand($sql)->execute();
    //echo '<pre>';
    	foreach($result as $index=>$row){
    		
       		$plot = Plots::model()->find('block_number = :block AND plot_type = :type AND plot_number = :number',array(':block'=>$row[0],':type'=>$row[1],':number'=>$row[2]));
       		if($plot){
       			PlotBoundries::model()->deleteAll("plot_id = $plot->id");
				if($row[11]){
					$pb = new PlotBoundries;
					$pb->plot_id = $plot->id;
					$pb->north = $row[11];
					$pb->west = $row[14];
					$pb->south = $row[12];
					$pb->east = $row[13];
					$pb->save(false);
				}
       		}
       }
       echo 'Done';
    }


	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}