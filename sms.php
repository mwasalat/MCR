<?php

		$message = rawurlencode("Dear Parent,

Reminder

Kindly Pay the School Bus fees  for your child within 7 days to avoid disruption in your child bus service

Thank you 
Fast Line Bus Rental.
");
		$nums = rawurlencode("971562472763,050-8113422,050-8113422,971504915812,971586225121,971503595646,971567985407,971559658957,971566455330,971501063016,971501242654,971502452732,971502452732,971527479050,971504736276,971557658191,971505660937,971551051653,971507214796,971502931324");
		$url = "http://180.179.218.150/sendurlcomma.aspx?user=20084826&pwd=FASTAACT@1&senderid=FRCAUHACCTS&mobileno=$nums&msgtext=$message&priority=High&CountryCode=+971";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curl_scraped_page = curl_exec($ch);
        curl_close($ch);
        echo $curl_scraped_page;

?>


