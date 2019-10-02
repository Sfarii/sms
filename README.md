School Management System
========================

School Management System is a is a complete school management software designed to automate a school's diverse operations from classes, exams to school events calendar. This school software has a powerful online community to bring parents, teachers and students on a common interactive platform.

Requirements
------------

  * PHP 7.1.3 or higher;
  * PDO-SQLite PHP extension enabled;
  * and the [usual Symfony application requirements][2].

Installation
------------

### Step 1 : Clone the project

Now clone the project from github.

```bash
$ git clone https://github.com/Sfarii/symfony-sms.git
```

### Step 2 : Install dependencies

Now that the project is cloned, running the following command should install all the symfony dependencies:

```bash
$ composer install
```

### Step 3 : Configuration

Now configure the parameters.yaml file under app under config.

### Step 4 : Run the project

Now run this command to run the built-in web server and access the application in your browser at <http://localhost:8000>:

```bash
$ php bin/console server:run
```

That's it.
