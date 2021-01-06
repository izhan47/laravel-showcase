<!DOCTYPE html>
<html lang="en">
    <head>
        <head>
            <title>Contact Mail</title>
        </head>
    </head>
    <body>
        <p>Contact details</p>        
        <p>Name:<br/>{{ $contact->name }}</p>       
        <p>Email:<br/>{{ $contact->email }}</p>
        <p>Message:<br/>{{ $contact->message }}</p>
        <br/>
        <p>Warm Regards,</p>
        <p>The Wag Enabled Team</p>            
    </body>
</html>