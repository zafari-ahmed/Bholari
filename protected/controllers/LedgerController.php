<?php

class LedgerController extends Controller
{
    public function actionBookingLedger($id)
	{
		$data['booking'] = $booking = CustomerPlots::model()->findByPk($id);
		$customLedger = 0;
		if($booking->customerpaymentSchedule){
			$customLedger = 1;
		}
		$this->layout = 'ledger';
		if($customLedger==1){
			if(@$_GET['type']=='transactions'){
				$this->render('ledger-transactions-custom',$data);	
			} elseif(@$_GET['type']=='dues'){
				$this->render('ledger-dues-custom',$data);
			} else{
				$this->render('ledger-custom',$data);
			}
		} else{
			if(@$_GET['type']=='transactions'){
				$this->render('ledger-transactions',$data);	
			} elseif(@$_GET['type']=='dues'){
				$this->render('ledger-dues',$data);
			} else{
				$this->render('ledger',$data);
			}	
		}
	}
	
	
	public function actionBookingLedgerbyplot($block,$plot)
	{
	    $plot = Plots::model()->find('block_number LIKE :block AND plot_number LIKE :number',array(':block'=>$block,':number'=>$plot));
	    if($plot){
	        $bookingId = '';
	        if($plot->customerPlots){
	            $bookingId = $plot->customerPlots[0]->id;
	        } 
	        if(!empty($bookingId)){
	         $data['booking'] = $booking = CustomerPlots::model()->findByPk($bookingId);
        		$customLedger = 0;
        		if($booking->customerpaymentSchedule){
        			$customLedger = 1;
        		}
        		$this->layout = 'ledger';
        		if($customLedger==1){
        			if(@$_GET['type']=='transactions'){
        				$this->render('ledger-transactions-custom',$data);	
        			} elseif(@$_GET['type']=='dues'){
        				$this->render('ledger-dues-custom',$data);
        			} else{
        				$this->render('ledger-custom',$data);
        			}
        		} else{
        			if(@$_GET['type']=='transactions'){
        				$this->render('ledger-transactions',$data);	
        			} elseif(@$_GET['type']=='dues'){
        				$this->render('ledger-dues',$data);
        			} else{
        				$this->render('ledger-plot',$data);
        			}	
        		}   
	        } else{
	            echo 'Not Booking Found';
	        }
	    }
	}
}