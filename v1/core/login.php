<?php

    /*
        creeds_api - login.py
        User login that returns their tokens
        
        Contribute on https://github.com/CreedsGame/creeds_api
    */

    header("Content-Type:application/json");
    require "../config.php";
    require "../game.php";
    require "../misc.php";

    # Create connection
    $conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

    # Check connection
    if ($conn) {

        # Charset to handle unicode
        $conn->set_charset('utf8mb4');
        mysqli_set_charset($conn, 'utf8mb4');

        # Get user and password
        if (!empty($_POST['user']) && !empty($_POST['pass']))
        {
            # Get user
            $user = build_str(clean_str($conn, $_POST['user']));

            # Get password
            $pass = clean_str($conn, $_POST['pass']);

            # Password's hash
            $pass_hash = build_str(string_to_hash($pass));

            # Prepare query to validate login
            $sql_query = "SELECT * FROM users WHERE userId = ".$user." AND password = ".$pass_hash."";

            # Execute query
            $result = mysqli_query($conn, $sql_query);

            # Check if there were results
            if (mysqli_num_rows($result) > 0)
            {
                # Prepare query to get user's tokens
                $sql_query = "SELECT * FROM api_tokens WHERE userId = ".$user."";

                # Execute query
                $result = mysqli_query($conn, $sql_query);

                # Empty array for user's tokens
                $tokens = [];

                # Check if there were results
                if (mysqli_num_rows($result) > 0)
                {
                    # Loop thru tokens
                    while ($row = mysqli_fetch_assoc($result))
                    {
                        # Build token data
                        $token = [
                            "token" => $row["token"],
                            "count" => (int)$row["count"],
                            "lastUsage" => $row["lastUsage"]
                        ];

                        # Push token to tokens array
                        array_push($tokens, $token);
                    }
                }

                # Return OK
                response(200, "ok", $tokens);
            }
            else
            {
                # Return error
                response(403, "Invalid username or password", NULL);
            }
        }
        else
        {
            # Return error
            response(403, "Unspecified username or password", NULL);
        }
    }
    else
    {
        # Return error
        response(500, "Unable to connect to the database", NULL);
    }
?>