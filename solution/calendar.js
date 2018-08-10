$(document).ready(function() {
	$.ajax({
		url: "test-task2.php",
		type: "GET",
		dataType: "json"
	}).done(function(data){
		//console.log(data);
		var n = 5,
			freeBusyDays = new Object();
		// Создание массива из свободных и занятых дней для брони
		for (key in data) {
			if (data[key] < n) {
				freeBusyDays[key] = 1;
			} else if (data[key] >= n) {
				freeBusyDays[key] = 0;
			}
		}

		var options = {
            	showAlways: true,
            	cssName: 'darkneon',
    			todayDate: new Date(2012, 2, 4), // ЗАМЕНА СЕГОДНЯШНЕЙ ДАТЫ НА 04-02-2012 для теста
    			//todayDate: new Date(), // Сегодня
    			selectedDate: null,
    			selectableDates: [

    			]
            },
        	i = 0,
        	date = new Object();
        // Добавление дней, на которые возможна бронь
        for (key in freeBusyDays) {
			if (freeBusyDays[key] == 1) {
				var dateObj = new Date(key),
					year = dateObj.getFullYear(),
					month = dateObj.getMonth() + 1,
					day = dateObj.getDate();

				options['selectableDates'][i] = new Object();
				options['selectableDates'][i]['date'] = new Date(year, month, day);
				i++;
			}
		};

        $('#mydate').glDatePicker(options);

	}).fail(function(){
		alert("Request failed"); 
	})
})
