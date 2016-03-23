# Introduction

Zsebtanár is a collection of interactive Maths exercises.

# Setup

You need to have a running *PHP Server* to run the website and a working internet connection for best display.

1. Download the repository.
2. Unzip the file and copy the `zsebtanar_v4` folder in your `public_html` folder (or `htdocs`, you are using *Xampp*).
3. Type in the following URLs your browser: `http://localhost/zsebtanar_v4/public/application/setup`.
4. The website can be reached through the URL: `http://localhost/zsebtanar_v4/`.

*Note:* If special characters don't apper properly, check the database character set. In order to use Hungarian special characters, use `latin2_hungarian_ci`.

# Log in
If you want to log in the website, click on "Admin" on the right side, and type in the password (zst). After logging in, you have additional features:

1. *Update database*: run this after adding a new exercise.
2. *Clear results*: delete points earned by user.
3. *Log out*: log out the website.

Without login, only exercises with `status=OK` will be displayed. After logging in, all exercises are displayed.

# Add new exercise
In order to add new exercise, you have to do the following steps:

1. Include exercise data in the JSON-file.
2. Create a PHP class to generate exercise.
3. Update database.

## STEP 1: Add exercise info to JSON-file

Exercises are stored in *public/resources/data.json*. The hierachy is the following:

1. Class
2. Topic
3. Subtopic
4. Exercise

Each of them *must* have a `name` attribute. Exercises and subtopics *must* have an additional `label` attribute, which is the name of the PHP class file (avoid space and accents). Exercises *can* have additional attributes:

- `level`: how many times user has to solve exercise to complete it (default: **9**)
- `status`: **OK** if exercise is finished (default: **IN PROGRESS**)
- `finished`: **DATE** in `YYYY-MM-DD` format (default: **(CURRENT DATE)**)

This is a sample for `data.json`:
```
{
    "classes": [
        {
            "name": "5. osztály",
            "topics": [
                {
                    "name": "Alapok",
                    "subtopics": [
                        {
                            "name": "Számolás",
                            "label": "Counting",
                            "exercises": [
                                {
                                    "label": "count_apples",
                                    "name": "Számolás 1-től 20-ig",
                                    "status": "OK"
                                },
                                {
                                    "label": "parity",
                                    "name": "Páros vagy páratlan?",
                                    "level": "4"
                                }
                            ]
                        }
                    ]
                }
            ]
        }
    ]
}
```

## STEP 2: Create PHP class to generate exercise

1. Create file `ExerciseClass.php` in the `/application/library/` folder where `ExerciseClass` is equal to `label` of the exercise in the JSON-file.
2. Define function called `Generate($level)`.
    - The input is *always* one parameter (`$level`), which is the level of exercise - this can be used to set the difficulty of the exercise.
    - Each exercise *must* be provided with the following return values:
        1. `$question`: the main body of the exercise,
        2. `$solution`: this is what the user will see if the answer is wrong.
        2. `$correct`: correct answer that will be used to compare the user's answer against.
    - Each exercise *can* be provided with additional return values:
        1. `$type`: exercise type,
        2. `$explanation`: explanation for exercise.

You can use **MathJax** to display formulas.

### Exercise types

You can create different types of exercise:

#### 1. Integer (default)

User has to send an integer as an answer. To use this you have to return an integer in the `$correct` variable.

Example:
```
/* Guess number */
class Guess_number {
    function Generate($level) {

        $num = rand($level, 3*$level);

        $question = 'How many is $2\cdot'.$num.'$?';
        $correct = 2*$num;
        $solution = '$'.$correct.'$'; // use MathJax for better display

        return array(
            'question'      => $question,
            'correct'       => $correct,
            'solution'      => $solution
        );
    }
}
```

#### 2. Quiz

User has to choose one answer for given options. To use this you have to return the following values:
1. `$options`: array containing options
2. `$correct`: key of correct option

Example:
```
/* Define parity of number */
class Parity {
    function Generate($level) {

        $num = rand($level, 3*$level);

        $question = 'Is the following even or odd?$$'.$num.'$$';

        $options = array('even', 'odd');
        $index = $num%2;
        $solution = $options[$index];

        shuffle($options); // shuffle options
        $correct = array_search($solution, $options); // search key of correct answer

        return array(
            'question'  => $question,
            'options'   => $options,
            'correct'   => $correct,
            'solution'  => $solution
        );
    }
}
```

#### 3. Multi
User has to choose one or more answer for given options. To use this you have to return the following values:
1. `$options`: array containing options
2. `$correct`: array of **0**s and **1**s (for wrong and correct options, respectively)

Example:
```
/* Classify square */
class Square {
    function Generate($level) {

        $question = 'What is a square?';
        $options = array('rectangle', 'parallelogram', 'circle');
        $correct = array(1, 1, 0);
        $solution = 'The square is a rectangle and a parallelogram but not a circle.';

        return array(
            'question'  => $question,
            'options'   => $options,
            'correct'   => $correct,
            'solution'  => $solution,
            'type'      => 'multi'
        );
    }
}
```
#### 4. Fraction
User has to return a fraction. To use this you have to return the following values:
1. `$correct`: array containing numerator and denominator

Example:
```
/* Define reciprocal of fraction */
class Reciprocal {
    function Generate($level) {

        $num = rand(1, $level);
        $denom = rand(1, $level);

        $question = 'What is the reciprocal of the following fraction?$$\frac{'.$num.'}{'.$denom.'}$$';
        $correct = array($denom, $num);
        $solution = '$\frac{'.$denom.'}{'.$num.'}$';

        return array(
            'question'  => $question,
            'correct'   => $correct,
            'solution'  => $solution,
            'type'      => 'fraction'
        );
    }
}
```
#### 5. Division
User has to return a quotient and a remainer. To use this you have to return the following values:
1. `$correct`: array containing quotient and remainer

Example:
```
/* Define quotient and remainer */
class Division {
    function Generate($level) {

        $dividend = rand(1, $level);
        $divisor = rand(1, $level);

        $quotient = ceil($dividend/$divisor);
        $remain = $dividend % $divisor;

        $question = 'What is the result of the following division?$$'.$dividend.':'.$divisor.'=?$$';
        $correct = array($quotient, $remain);
        $solution = 'The quotient is $'.$quotient.'$ and the remain is $'.$remain.'$';

        return array(
            'question'  => $question,
            'correct'   => $correct,
            'solution'  => $solution,
            'type'      => 'division'
        );
    }
}
```
#### 6. Custom types
In order to add a custom type:

1. Choose name for type (e.g. *custom*)
2. Create new display in `application->Views->Input->Custom.php`
3. Add custom display in `application->Views->Body->Exercise`
4. Define way to compare user's answer to correct solution in `application->Models->Check->GenerateMessages`


### Hints
You can generate hints for exercises. In this case you have to return an extra variabel in the end of the function, e.g.:
```
return array(
    'question'  => $question,
    'correct'   => $correct,
    'solution'  => $solution,
    'hints'     => $hints
);
```
The structure of the hints can be the following:

#### Single-page
In single-page mode hints will be displayed under each other. In this case the variable `$hints` must be an array containing the hints. E.g.:
```
$hints[] = 'This is hint one.';
$hints[] = 'This is hint two.';
$hints[] = 'This is hint three.';
```
#### Multi-page
In multi-page mode hints of the next page will replace earlier hints. In this case the variable `$hints` must be an array containing subarrays, where each subarray contains the hints for the given page. E.g.:
```
$page[] = 'This is hint 1 on page 1.';
$page[] = 'This is hint 2 on page 1.';
$page[] = 'This is hint 3 on page 1.';
$hints[] = $page;

$page = [];
$page[] = 'This is hint 1 on page 2.';
$page[] = 'This is hint 2 on page 2.';
$page[] = 'This is hint 3 on page 2.';
$hints[] = $page;
```
#### Details
If you want to provide details for a specific hint, you need to add an array after the hint. E.g.:
```
$hints[] = 'This is a hint.';
$hints[] = array('This is', 'some details', 'about the hint.');
```
The program will concatenate the detail elements and add a button after the hint. If the user clicks on the hint, he will see the details.

### Additional features
#### 1. Using pictures
In order to include picture into exercise:

1. Upload pictures in `public->resources->exercises` folder
2. Include picture using *base_url()* function. E.g.:

```
$question = 'How many apples are there in the tree?
    <div class="text-center">
        <img class="img-question" height="200px" src="'.base_url().'resources/exercises/count_apples/tree1.png">
    </div>';
```

To generate pictures, you can use the **SVG** functions (see more: http://www.w3schools.com/svg/default.asp)

#### 2. Built in functions
You can find language functions in the `application->helpers->language_helper.php` file (this is very useful for Hungarian exercises).

You can find mathematical functions in the `application->helpers->language_helper.php` file.

If you have a function called `function($par1, $par2)` in either of these files, you can invoke them in your class file.

# Contact

- Website: http://www.zsebtanar.hu
- Email: zsebtanar@gmail.com
