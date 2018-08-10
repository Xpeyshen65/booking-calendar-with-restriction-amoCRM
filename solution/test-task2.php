<?php

require_once ('../intr-sdk-test/autoload.php');

function countLeadsForSetDates($api, $statuses = array(), $idDateField, $date, $offset) {
	$crm_user_id = null; // int[] | фильтр по id ответственного
	$countLeadsForSetDates = array();

	if (is_string($date)) {
		// Получение timestamp
		$dateTimeBeginDay = strtotime($date . ' 00:00:00');
		$dateTimeEndDay = strtotime($date . ' 23:59:59');
	} elseif (is_int($date)) {
		$dateTimeBeginDay = $date;
		$dateTimeEndDay = $date + 86399; // Прибавление количества секунд от 00:00:00 до 23:59:59
	} else {
		$data = "Неверный формат даты";
		file_put_contents('log.txt', $data, FILE_APPEND | LOCK_EX);
		return FALSE;
	}

	try {
	   	$result = $api->lead->getAll($crm_user_id, $statuses);
	   	for ($i=0; $i < $offset ; $i++) {
	   		$countLeads = 0;
	   		foreach ($result['result'] as $key => $value) {
		   		/*if ($value['custom_fields'][$idDateField] == $date) {
		   			$countLeads++;
		   		}*/
		   		if ( ($value['date_create'] >= $dateTimeBeginDay) && ($value['date_create'] <= $dateTimeEndDay) ) {
		   			$countLeads++;
	   			} 		
	   		}
	   		$keyDate = date("n-j-Y" ,$dateTimeBeginDay); // M-D-YYYY
	   		$countLeadsForSetDates[$keyDate][] = $countLeads;
	   		$dateTimeBeginDay = strtotime("+1 day", $dateTimeBeginDay);
	   		$dateTimeEndDay = strtotime("+1 day", $dateTimeEndDay);
	   	}
	   	return $countLeadsForSetDates;
	} catch (Exception $e) {
		$data = array('Exception when calling lead->getAll: ', $e->getMessage(), PHP_EOL);
		file_put_contents('log.txt', $data, FILE_APPEND | LOCK_EX);
		return FALSE;
	}
} // Подсчет количества сделок для каждой даты на следующие n дней

Introvert\Configuration::getDefaultConfiguration()->setApiKey('key', 'YOUR_API_KEY');

$api = new Introvert\ApiClient();

$statuses = array(15175276, 15224578); // int[] | фильтр по id статуса
$idDateField = null; // id пользовательского поля с датой
// ЗАМЕНА СЕГОДНЯШНЕЙ ДАТЫ НА 04-02-2012 для теста
$date = '04-02-2012'; // MM/DD/YYYY or DD-MM-YYYY or timestamp
// Сегодня
//$date = strtotime("today"); // MM/DD/YYYY or DD-MM-YYYY or timestamp
$offset = 30; // Проверка следующих n дней
$countLeadsForSetDates = countLeadsForSetDates($api, $statuses, $idDateField, $date, $offset);

if ($countLeadsForSetDates != FALSE) {
	echo json_encode($countLeadsForSetDates);
}


