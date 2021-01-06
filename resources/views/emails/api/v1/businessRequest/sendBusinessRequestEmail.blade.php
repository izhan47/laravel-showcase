<!DOCTYPE html>
<html lang="en">
    <head>
        <head>
            <title>Business Request Mail</title>
        </head>
    </head>
    <body>
        <p>Business Request Details</p>        
        <p>First Name:<br/>{{ $businessRequest->first_name }}</p>       
        <p>Last Name:<br/>{{ $businessRequest->last_name }}</p>       
        <p>Business Name:<br/>{{ $businessRequest->business_name }}</p>       
        <p>Contact Email:<br/>{{ $businessRequest->contact_email }}</p>       
        <p>Message:<br/>{{ $businessRequest->message }}</p>
        <br/>
        <p>Warm Regards,</p>
        <p>The Wag Enabled Team</p>            
    </body>
</html>