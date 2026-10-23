<?php

class PlotController extends Controller
{
	public function actionAdd()
	{
		$data['categories'] = PlotCategories::model()->findAll();
		//$data['sizes'] = PlotSizes::model()->findAll();
		$criteria = new CDbCriteria();
        $criteria->select = 'MIN(id) AS id, size';
        $criteria->group = 'size';
        $criteria->order = 'id ASC';
        $data['sizes'] = PlotSizes::model()->findAll($criteria);
		$this->render('add',$data);
	}

	public function actionSave(){
		if($_POST['plot_number']){
			$plotCheck = Plots::model()->find('plot_number=:plot AND block_number = :block',array(':plot'=>$_POST['plot_number'],':block'=>$_POST['block_number']));
			if(!$plotCheck){
				$plot = new Plots;
				$plot->attributes = $_POST;
				$plot->is_road_facing_amount = 10;
				$plot->is_corner_amount = 10;
				$plot->is_park_facing_amount = 10;
				$plot->is_west_open_amount = 10;
				$plot->is_corner = isset($_POST['is_corner'])?1:0;
				$plot->is_park_facing = isset($_POST['is_park_facing'])?1:0;
				$plot->is_west_open = isset($_POST['is_west_open'])?1:0;
				$plot->is_road_facing = isset($_POST['is_road_facing'])?1:0;
				$plot->phase_id = Yii::app()->session->get('userModel')['phase_id'];
				$plot->status = 0;
				$plot->save(false);
				if(isset($_FILES['site_plan'])){
    				if($_FILES['site_plan']['name']){
    					PlotSitePlans::model()->deleteAll("plot_id = $plot->id");
    					$uploadFolder = getcwd() . '/uploads/plot/site_plan/';
    					$fileName = time().'_'.$_FILES['site_plan']['name'];
    			        move_uploaded_file($_FILES['site_plan']['tmp_name'], $uploadFolder.$fileName);
    			        $psp = new PlotSitePlans;
    					$psp->plot_id = $plot->id;
    					$psp->site_plan = $fileName;
    					$psp->image = 'null';
    					$psp->save(false);
    				}
    				if($_POST['north']){
    					PlotBoundries::model()->deleteAll("plot_id = $plot->id");
    					$pb = new PlotBoundries;
    					$pb->plot_id = $plot->id;
    					$pb->north = $_POST['north'];
    					$pb->west = $_POST['west'];
    					$pb->south = $_POST['south'];
    					$pb->east = $_POST['east'];
    					$pb->save(false);
    				}
				}
				Yii::app()->user->setFlash('success','Plot has been saved.');
	            $this->redirect(Yii::app()->baseUrl.'/plot');	
			} else{
				Yii::app()->user->setFlash('error','Plot already saved.');
	             $this->redirect(Yii::app()->baseUrl.'/plot');
			}
			
		}
	}

	public function Percentage($total,$percentage,$view = 1){
		if($view == 1){
			return number_format((@$percentage / 100) * @$total);
		} else{
			return (@$percentage / 100) * @$total;
		}
		
	}

	public function plotTotal($id,$number_format = true,$is_total = true){
		$plot = Plots::model()->findByPk($id);
		$total = 0;
		if($is_total){
			$total += $plot->total;	
		}
		
		if($plot->is_road_facing == 1){
			$total += $this->Percentage($plot->total,$plot->is_road_facing_amount,0);
		}
		if($plot->is_park_facing == 1){
			$total += $this->Percentage($plot->total,$plot->is_park_facing_amount,0);	
		}
		if($plot->is_corner == 1){
			$total += $this->Percentage($plot->total,$plot->is_corner_amount,0);
		}
		if($plot->is_west_open == 1){
			$total += $this->Percentage($plot->total,$plot->is_west_open_amount,0);
		}
		if($number_format){
			return number_format($total);	
		} else{
			return $total;
		}
		
	}

	public function plotDiscount($id,$number_format = true){
		$plot = Plots::model()->findByPk($id);
		$totalDiscount = 0;
		
		$total = $this->plotTotal($id,false);
		$totalDiscount = $this->Percentage($total,$plot->discount,0);
		//$total = $total - $totalDiscount;
		if($number_format){
			return number_format($totalDiscount);	
		} else{
			return $totalDiscount;
		}
		
	}

	public function actionIndex(){
// 	{	$phaseId = Yii::app()->session->get('userModel')['phase_id'];
// 		if(isset($_GET['status'])){
// 			$s = $_GET['status'];
// 			$data['plots'] = Plots::model()->findAll("status = $s AND phase_id = $phaseId");
// 		} else{
// 			$data['plots'] = Plots::model()->findAll("phase_id = $phaseId");
// 		}
		
// 		$this->render('index',$data);

        $this->render('index-fetchall');
	}

	public function actionView($id)
	{
		$data['plot'] = Plots::model()->findByPk($id);
		$this->render('view',$data);
	}


	public function actionEdit($id)
	{
		$data['plot'] = Plots::model()->findByPk($id);
		$data['categories'] = PlotCategories::model()->findAll();
		$data['sizes'] = PlotSizes::model()->findAll();
		$this->render('edit',$data);
	}

	public function actionUpdate(){
		//echo '<pre>';print_r($_POST);exit;
		if($_POST['plot_number']){
			$plot = Plots::model()->findByPk($_POST['id']);
			$plot->attributes = $_POST;
			$plot->is_corner = isset($_POST['is_corner'])?1:0;
			$plot->is_park_facing = isset($_POST['is_park_facing'])?1:0;
			$plot->is_west_open = isset($_POST['is_west_open'])?1:0;
			$plot->is_road_facing = isset($_POST['is_road_facing'])?1:0;
			$plot->phase_id = Yii::app()->session->get('userModel')['phase_id'];
			$plot->save(false);
			if(isset($_FILES['site_plan'])){
    			if($_FILES['site_plan']['name']){
    				PlotSitePlans::model()->deleteAll("plot_id = $plot->id");
    				$uploadFolder = getcwd() . '/uploads/plot/site_plan/';
    				$fileName = time().'_'.$_FILES['site_plan']['name'];
    		        move_uploaded_file($_FILES['site_plan']['tmp_name'], $uploadFolder.$fileName);
    		        $psp = new PlotSitePlans;
    				$psp->plot_id = $plot->id;
    				$psp->site_plan = $fileName;
    				$psp->image = 'null';
    				$psp->save(false);
    			}
    			if($_POST['north']){
    				PlotBoundries::model()->deleteAll("plot_id = $plot->id");
    				$pb = new PlotBoundries;
    				$pb->plot_id = $plot->id;
    				$pb->north = $_POST['north'];
    				$pb->west = $_POST['west'];
    				$pb->south = $_POST['south'];
    				$pb->east = $_POST['east'];
    				$pb->save(false);
    			}
			}
			Yii::app()->user->setFlash('success','Plot has been Updated.');
            $this->redirect(Yii::app()->baseUrl.'/plot');
		}
	}

	public function actionGetpaymentschedule($id,$booking){
		$this->layout = 'ledger';
		$data['plot'] = Plots::model()->findByPk($id);
		$data['booking'] = CustomerPlotsPreview::model()->findByPk($booking);
		$data['is_orig'] = '0';
		$this->render('payment_schedule',$data);

	}

	public function actionGetpaymentscheduleOrig($id,$booking){
		$this->layout = 'ledger';
		$data['plot'] = Plots::model()->findByPk($id);
		$data['booking'] = CustomerPlots::model()->findByPk($booking);
		$data['is_orig'] = '1';
		$this->render('payment_schedule',$data);

	}

	public function getIndianCurrency(float $number)
	{
	    $decimal = round($number - ($no = floor($number)), 2) * 100;
	    $hundred = null;
	    $digits_length = strlen($no);
	    $i = 0;
	    $str = array();
	    $words = array(0 => '', 1 => 'one', 2 => 'two',
	        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
	        7 => 'seven', 8 => 'eight', 9 => 'nine',
	        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
	        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
	        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
	        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
	        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
	        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
	    $digits = array('', 'hundred','thousand','lac', 'crore');
	    while( $i < $digits_length ) {
	        $divider = ($i == 2) ? 10 : 100;
	        $number = floor($no % $divider);
	        $no = floor($no / $divider);
	        $i += $divider == 10 ? 1 : 2;
	        if ($number) {
	            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
	            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
	            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
	        } else $str[] = null;
	    }
	    $Rupees = implode('', array_reverse($str));
	    $paise = ($decimal) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
	    return ($Rupees ? $Rupees . ' Rupees Only ' : '') . $paise;
	}
	
	
	public function actionFetchall()
    {
        $userModel = Yii::app()->session->get('userModel');
        $phaseId   = $userModel['phase_id'];
    
        $result_array = [];
        parse_str($_SERVER['REQUEST_URI'], $result_array);
    
        $criteria = new CDbCriteria();
        $criteria->with = ['category','size','agentReserve.agent.agentParent'];
    
        // Sorting
        switch (@$result_array['order'][0]['column']) {
            case '1':
                $sortColumn = 't.plot_number';
                break;
            case '2':
                $sortColumn = 'category.name';
                break;
            case '3':
                $sortColumn = 'size.size';
                break;
            default:
                $sortColumn = 't.id';
                break;
        }
    
        $sortDir = @$result_array['order'][0]['dir'] ?: 'DESC';
    
        $criteria->order  = $sortColumn . ' ' . $sortDir;
        $criteria->limit  = @$result_array['length'];
        $criteria->offset = @$result_array['start'];
    
        // Base Condition
        $criteria->addCondition("t.phase_id = :phase");
        $criteria->params[':phase'] = $phaseId;
    
        // Status Filter
        if(isset($result_array['status']) && $result_array['status'] != ''){
            $criteria->addCondition("t.status = :status");
            $criteria->params[':status'] = $result_array['status'];
        }
    
        // Search
        if(!empty(@$result_array['search']['value'])){

            $search = trim($result_array['search']['value']);
        
            if(strpos($search, '-') !== false){
        
                $parts = explode('-', $search);
        
                if(count($parts) >= 2){
        
                    // Plot = last part
                    $plotNumber = trim(array_pop($parts));
        
                    // Block = remaining joined
                    $blockNumber = trim(implode('-', $parts));
        
                    // Always apply block if exists
                    if($blockNumber !== ''){
                        $criteria->addCondition('t.block_number = :blockNumber');
                        $criteria->params[':blockNumber'] = $blockNumber;
                    }
        
                    // Apply plot ONLY if not empty
                    if($plotNumber !== ''){
                        $criteria->addCondition('t.plot_number = :plotNumber');
                        $criteria->params[':plotNumber'] = $plotNumber;
                    }
                }
        
            } else {
        
                // Normal fallback search
                $criteria->addSearchCondition('t.plot_number', $search, true, 'OR');
                $criteria->addSearchCondition('t.block_number', $search, true, 'OR');
            }
        }

    
        //echo '<pre>';print_r($criteria);exit;
        // Fetch Data
        $model = Plots::model()->findAll($criteria);
    
        // Count Total
        $countCriteria = clone $criteria;
        $countCriteria->limit  = -1;
        $countCriteria->offset = -1;
        $total = Plots::model()->count($countCriteria);
    
        // Result Format
        $result = [];
        $result['draw']            = intval(@$result_array['draw']);
        $result['recordsTotal']    = $total;
        $result['recordsFiltered'] = $total;
        $result['data']            = [];
    
        if($model){
            foreach($model as $plot){
    
                // Dealer
                $dealer = '-';
                if(!empty($plot->agentReserve[0])){
                    $dealer =
                        @$plot->agentReserve[0]->agent->agentParent->name .
                        '/' .
                        @$plot->agentReserve[0]->agent->name;
                }
    
                // Status
                $status = ($plot->status == 0)
                    ? '<span class="label label-success">Available</span>'
                    : '<span class="label label-danger">Booked</span>';
    
                // Buttons
                $buttons  = '<a href="'.Yii::app()->baseUrl.'/plot/view/'.$plot->id.'">
                                <span class="aLink label label-info">View</span>
                             </a>&nbsp;';
    
                if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){
                    $buttons .= '<a href="'.Yii::app()->baseUrl.'/plot/edit/'.$plot->id.'">
                                    <span class="aLink label label-warning">Edit</span>
                                 </a>';
                }
    
                // Features Table
                $features = '
                <table class="table table-responsive" style="font-size:12px;">
                    <tr>
                        <td class="tbBold">Corner</td>
                        <td>'.($plot->is_corner ? 
                            '<span class="label label-success">YES</span>' :
                            '<span class="label label-danger">NO</span>').'
                        </td>
                    </tr>
                    <tr>
                        <td class="tbBold">Park Facing</td>
                        <td>'.($plot->is_park_facing ? 
                            '<span class="label label-success">YES</span>' :
                            '<span class="label label-danger">NO</span>').'
                        </td>
                    </tr>
                    <tr>
                        <td class="tbBold">West Open</td>
                        <td>'.($plot->is_west_open ? 
                            '<span class="label label-success">YES</span>' :
                            '<span class="label label-danger">NO</span>').'
                        </td>
                    </tr>
                </table>';
    
                $result['data'][] = [
                    //$plot->id,
                    '<a href="'.Yii::app()->baseUrl.'/plot/view/'.$plot->id.'">
                        *'.$plot->block_number.'-'.$plot->plot_number.'*
                     </a>',
                    @$plot->category->name,
                    @$plot->size->size,
                    @$plot->total,
                    @$plot->discount,
                    $dealer,
                    $status.'<br>'.$buttons.$features
                ];
            }
        }
    
        echo json_encode($result);
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