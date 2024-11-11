<!DOCTYPE html>
<html>
    <head>
        <title>Contact Message</title>
    </head>
    <body>
        <h2>New Contact Message</h2>
        <p>
            <strong>Name:</strong>
            {{ $messageData['name'] }}
        </p>
        <p>
            <strong>Phone:</strong>
            {{ $messageData['phone'] }}
        </p>
        <p>
            <strong>Email:</strong>
            {{ $messageData['email'] }}
        </p>
        <p>
            <strong>Subject:</strong>
            {{ $messageData['subject'] }}
        </p>
        <p>
            <strong>Message:</strong>
            {{ $messageData['message'] }}
        </p>
    </body>
</html>
