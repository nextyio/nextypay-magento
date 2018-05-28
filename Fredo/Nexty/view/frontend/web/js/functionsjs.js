

//require(['jquery'], function($){ /* Code... */
/////////////////////////
function countInSecond(startTime,endTime) {
  var timeDiff = endTime - startTime; //in ms
  // strip the ms
  timeDiff /= 1000;

  // get seconds
  var seconds = Math.round(timeDiff);
  return seconds;
}

function call_ajax(url,startTime,order_id,timeout,interval){
	var seconds=countInSecond(startTime,new Date());
	console.log(seconds);
	if (seconds>timeout) {
		console.log("time out");
		return;
	}
	var paid="0";
	setTimeout(function(){
		// This does the ajax request
		jQuery.ajax({
			url : url,
			type: 'POST',
      data: {
        order_id: order_id
      },
		}).done(function ( response ) {
			console.log(response);
			//alert (response);
			paid=response;
			if (paid[0]=="0") { return call_ajax(url,startTime,order_id,timeout,interval); } else
			{
				//console.log(response);
        //DO SMT
        require(['jquery', 'jquery/ui'], function($){
          $("#waiting_payment").html("Payment succeeded. Thank you and have fun with your Shopping!");
        });
				return;
			}
		}).fail(function ( err ) {

		})
	}, interval*1000);
}
///////////////////////////
//})
