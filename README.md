# PUSH NOTIFICATIONS

The mini application sends push notifications to all users by country. The push notification contains a title and a message. It sends to the user's device by device token.
There are 3 "actions" in the application:
- ``send`` saves the push notification in the database and puts it in the queue. We send push notifications by CRON action.
- ``details`` displays detailed information about the push notification.
- ``cron`` sends the push notification(s) to 100K devices.

### What you need to do:
1. Create the necessary tables to save and send push notifications.
2. Implement App\Controllers\PushNotificationController->sendByCountryId()
3. Implement App\Controllers\PushNotificationController->details()
4. Implement App\Controllers\PushNotificationController->cron()

#### Keep in mind:
- The user can have multiple devices.
- The device token can be expired (expired = 1).
- The **cron** action runs automatically every minute. So it's 100k devices per 1 minute.
- Use App\models\PushNotification::send() to send a push notification. It returns random value true/false.
- Read the documentation to understand the task: {project}/documentation/index.html
- Use the postman collection for testing: {project}/postman_collection.json
- Feel free to ask about anything if the task is not clear.

#### Requirements:
**PHP 7.4+**, **PDO** and no frameworks/libraries/wrappers, only your code and SQL queries.
The app should not only work, but also **work fast and safely**. Think about the **optimization**.

---

### Get started
1. Clone the repo: 
```
  git clone https://x-token-auth:ATCTT3xFfGN0MvdiqxwZy89g4Sx7ppHHLbxsArNIux8A_r1YhbNmDqf3ZMy-ZTXx8U8yAMrlcv9YWbwb9Lx9HmXE_5hlnVJYeNdGXE_NqZvHgPfSEd1izqYiaLRE9AopCQF3WQw1UDTrDL3Ru8vmR72Zsw9YMNhwHkHFpk8IK6I00_4r_rrcgcc=08E86936@bitbucket.org/levantsoft/push-notifications.git
```
2. Install:
```
  composer install
```
3. Create and configure .env file:
```
  cp .env.example .env
```
4. Run migrations and fill your database:
```
  vendor/bin/phinx migrate
  vendor/bin/phinx seed:run -s CountriesSeeder -s UsersSeeder -s DevicesSeeder
```

--- 

### When you're done
Send a link of your repository to s.mnatcakanian@gasable.com when you're done. Let us know how many hours you spent on this task.
