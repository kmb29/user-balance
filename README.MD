Test project By: Gregory Petrov

To run this project in Docker:

**composer install**

**docker-compose -f compose.yaml up -d**

Perform migrations:

**docker-compose exec laravel.test php artisan migrate**

Then you can test the API in Postman.

**Adding funds to the user:**

POST /api/deposit { "user_id": 1, "amount": 500.00, "comment": "Thank you!" }

**Withdrawing funds:**

POST /api/withdraw { "user_id": 1, "amount": 200.00, "comment": "Buying smth" }

(Balance cannot be negative)

**Transferring funds between users:**

POST /api/transfer { "from_user_id": 1, "to_user_id": 2, "amount": 150.00, "comment": "Happy birthday!" }

**Getting balance:**

GET /api/balance/{user_id} { "user_id": 1, "balance": 350.00 }

**Routes except /api/... are not handled by api application.**

**This requirements are considered:**

• The project must be implemented in PHP 8+.

• Data storage — in PostgreSQL.

• Upload the project to GitHub with a Readme

• The application must be deployed in Docker

• All financial transactions must be performed in transactions.

• The balance cannot be negative.

• If the user does not have a balance record, one is created upon the first deposit.

• All responses and errors must be in JSON format, with valid HTTP codes.

◦ 200 — successful response

◦ 400 / 422 — validation errors

◦ 404 — user not found

◦ 409 — conflict (e.g., insufficient funds)

• A transaction has the following statuses: deposit, withdraw, transfer_in, transfer_out
