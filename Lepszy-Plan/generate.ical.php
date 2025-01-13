<?php

require 'db_connection.php';  


$sql = "SELECT * FROM your_table LIMIT 5";  

$result = $conn->query($sql);


if ($result->num_rows > 0) {
    
    $ical = "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//Your Company//NONSGML v1.0//EN\n";
    
    
    while ($row = $result->fetch_assoc()) {
        $startTime = date('Ymd\THis\Z', strtotime($row['start_time']));  
        $endTime = date('Ymd\THis\Z', strtotime($row['end_time'])); 
        $summary = $row['name'];  
        $location = $row['location'];  

        $ical .= "BEGIN:VEVENT\n";
        $ical .= "SUMMARY:$summary\n";
        $ical .= "DTSTART:$startTime\n";
        $ical .= "DTEND:$endTime\n";
        $ical .= "LOCATION:$location\n";
        $ical .= "DESCRIPTION:$summary\n";
        $ical .= "END:VEVENT\n";
    }
    
    $ical .= "END:VCALENDAR";

    
    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="schedule.ics"');
    echo $ical;
} else {
    echo "No records found";
}

$conn->close();
?>
