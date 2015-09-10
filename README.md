# Moodle API
Provides a REST API to allow external software to access data from Moodle.

## Setup
```bash
cd /path/to/moodle
git clone https://github.com/antriver/moodle-local_api.git local/api
```

## Endpoints

### POST /local/api/auth.php

Authenticates users. [Read this to authenticate with PAM](docs/PAM.md)

#### Request

| Parameter | Required? | Details                                                                                                                                                                                                                                                         |
|-----------|-----------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| username  | Yes       |                                                                                                                                                                                                                                                                 |
| password  | Yes       |                                                                                                                                                                                                                                                                 |
| mode      |           | Options:<br>**post** (Default): POSTed username and password are checked. JSON is returned.<br>**pam**: POSTed username and password are checked. The text 'OK' is returned if valid.<br>**http**: HTTP authentication username and password are checked. JSON is returned. |

#### Response

##### Success
```
{
  "user": {
    "id": "901",
    "idnumber": "99999",
    "username": "happystudent",
    "email": "happystudent@email.com",
    "auth": "manual",
    "firstname": "Happy",
    "lastname": "Student"
  }
}
```
##### Error
```
{
  "error": "Incorrect password"
}
```
