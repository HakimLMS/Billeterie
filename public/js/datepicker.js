  $(document).ready(function() {
        
        
    var d = new Date();
    var hour = d.getHours();
    var day = d.getDate();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();
    if (day < 10) {
        day = "0" + day;
    }
    if (month < 10) {
        month = "0" + month;
    }
    var today = day + "/" + month + "/" + year;
		
    $( ".datepicker-book" ).datepicker({
			showOn: "focus",
			changeMonth: true,
			changeYear: true,
                        minDate: 0,
			showWeek: false,
   			beforeShowDay: DisabledDays,                                                 
                        showAnim: "show",
                        onSelect: manageTypeTickets
    });
                
    var FreeDays = ['05-01', '11-01', '12-25'];  
    
    function DisabledDays(date)
    {
         var  j = date.getDate(), m = date.getMonth(), a = date.getFullYear();
         var weeklyOff = date.getDay();
        
         if($.inArray((m + 1) + '-' + j,FreeDays) != -1 || weeklyOff== 2)
         {
            return [false];
         }
         return [true];
        
    }
    
    function manageTypeTickets()
    {

        var inputWatched = $('.datepicker-book').val();
        var a = $('.typePick option[value=1]');
        var b = $('.typePick option[value=0]');
        
        if (hour >= 14 && inputWatched === today)
        {
           
           a.prop( "disabled", true );
           b.prop("selected", true);
        }
        else
          a.prop( "disabled", false );
        
    }
    

  });