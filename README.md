## Trip Api

A REST API to get activities planned per day. The Api uses Laravel framework for PHP.

## Pre-requisities
1. PHP >= 7.1.0
2. Install composer https://getcomposer.org/

Laravel is already included in composer.json and should be installed by composer update command.

## How to setup?
1. Install php on your machine
 
   PHP installations on different platforms please refer to `http://php.net/manual/en/install.php`
   
2. Install composer
   The below script will simply check some php.ini settings, warn you if they are set incorrectly, and then download the
   latest composer.phar in the current directory. The 4 lines above will, in order:

   - Download the installer to the current directory
   - Verify the installer SHA-384 which you can also cross-check here
   - Run the installer
   - Remove the installer
   ```
   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
   php -r "if (hash_file('sha384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
   php composer-setup.php
   php -r "unlink('composer-setup.php');"
    ```
    
   For further information refer `https://getcomposer.org/`
   
3. I have chosen No SQL DB - MongoDB to store the activities file.
   To use Mongodb for storage of activities, install mongoDb
   
   `https://docs.mongodb.com/manual/administration/install-community`
   
   Example : On Mac , using homebrew follow the steps -
   
   - Update homebrew
   `brew update`
   
   - Install MongoDB Binaries : `brew install mongodb`
   
   - Before you start MongoDB for the first time, create the directory to which the mongod process will write data. 
     By default, the mongod process uses the /data/db directory. If you create a directory other than this one, you must
     specify that directory in the dbpath option when starting the mongod process later in this procedure.
    
     The following example command creates the default /data/db directory:

     `mkdir -p /data/db`
     
   - Before running mongod for the first time, ensure that the user account running mongod has read and write
     permissions for the directory.
     
   - Run MongoDB
   
     - To run MongoDB, run the mongod process at the system prompt:
     
       `mongod`
       
     - Specify the path of the mongod
     
       If your PATH does not include the location of the mongod binary, enter the full path to the mongod binary at the 
       system prompt:
       
       `<path to binary>/mongod`
       
       Specify the path of the data directory
       If you do not use the default data directory (i.e., /data/db), specify the path to the data directory using the 
       --dbpath option:
       
       `mongod --dbpath <path to data directory>`
     
   - Verify that MongoDB has started successfully by checking the process output for the following line:
     
     `[initandlisten] waiting for connections on port 27017`
     
4. Install dependencies (important !)
     
     `composer update`
          
5. Load class map
   
   `composer dumpautoload -o`
         
6. Start PHP built-in web server :http://php.net/manual/en/features.commandline.webserver.php
       
       php -S localhost:8000 
       
            or
            
       php artisan serve
       
7. Add correct settings to .env for mongodb
   If running on local, mongo host would be localhost (127.0.0.1) and mongo port 27017.
   If running inside docker, run docker-ps to get the port on which mongo is running. But please check
   your machine ip and ports and fill the correct config.
     
8. Run seeder to insert data in mongodb

   ```
    php artisan db:seed --class=BerlinActivitiesSeeder
   ```    

9. To Clear database if product team gives new list, run -
   
   ```
    php artisan db:seed --class=RemoveBerlinActivitiesSeeder
   
   ```
  
## Invoke api

   The planner api endpooint can be invoked as -

   ```
   curl -A GET 'http://localhost:8000/planner?budget=680&days=2'
   ```

## Assumptions/Considerations

1. The currency of all activities and input budget is assumed to be same

2. The default city is Berlin and default country of search is Germany. There params could be used
   in future for other cities.
   
3. Greedy algorithm is used to populate the activities. The algorithm is greedy on the number of activities that can
   be performed each day. Since the algorithm is greedy, it does not go back and reverse the decision to add
   another package combination to obtain the optimal solution.     

4. If Api fails to meet any of the constraints mentioned in requirements, a 400 status is returned -

   - Minimum activities output per day is 3
   - Input Budget should be between 100 and 5000 inclusive
   - Budget per day should be at least 50
   - Total input days should be between 1 to 5 inclusive
   - There should be 30 min commute time between activities
   - All activities each day start from 10:00 at intervals of 30 mins
   - Last activity each day has to be completed before 22:00 hrs   

5. The database of activities is sent by product team and is inserted in mongodb in this implementation
   But just by changing the `connection` in Activity.php, it can be inserted in another database. The configuration
   of that database key has to be added to config/database.php and give correct .env values for connection variables.      

## Ideal solution

   - The current implementation is greedy on the number of activities performed each day. It sorts the activities in 
       ascending order of price which naturally gives us shorter duration activities first ( less duration => less price) 
       and hence maximum activities can be accommodated in 1 day.
       
       However the drawback is, if large budget is given in input, only small amount of it gets used. This might be not
       suitable for the business. To draw money from the customers, the activity provider API should ideally give a mix
       of low cost, mid cost and expensive activities per day. One way to achieve this is to divide the data set in 3 ranges:
       - low
       - mid
       - high
       
       Iterate over the 3 ranges to pick mix of activities from each until time runs out each day.
   
   - In current implementation, if many short duration activities are picked up, the total relocation time increases.
     Ideally customers would want few longer duration tasks to reduce the relocation time.

## Running tests
   Run tests using codeception -
   ```
   bin/codecept build
   ```
   
   Run seeder to insert records
   ```
   php artisan db:seed --class=BerlinActivitiesSeeder
   ```
      
   Execute tests -
   ```
   bin/codecept run tests/api/TripPlannerCest.php
   ```
## System specifications used for development/ testing
   Web server nginx:1.15.3-alpine
   
   mvertes/alpine-mongo:4.0.1-0
   
## Problem statement
1. Product team gives list of activities. Each activity has a id, price, duration
   Activities start from duration 5-240

2. There is 30 min commute time between each activity. Activites start at 10:00 and last one should
   end before 22:00. Activities start from 10:00 every 30 mins. So if last activity ends at 10:10, next will
   start at 11:00 (10.00 + 30 = 10:40 & next 30 min interval is 11:00)

3. Budget can be int between 100 to 5000 inclusive. Days is int between 1 to 5 inclusive
   Min. budget per day is 50
   
4. Min. 3 activities should be output each day.

5. For simplicity, assume all activities are only in Berlin city.

6. Return bad request (400) if any of above conditions are not met

7. The input and output of api should be like -
   `http://localhost:8000/planner?budget=680&days=2`
   
   Response -
   `
  {
  	"schedule": {
  		"summary": {
  			"budget_spent": 578,
  			"time_in_relocation": 210,
  			"total_activities": 9
  		},
  		"days": [{
  			"day": 1,
  			"itinerary": [{
  				"start": "10:00",
  				"activity": {
  					"id": 6679,
  					"duration": 60,
  					"price": 17
  				}
  			}, {
  				"start": "11:30",
  				"activity": {
  					"id": 6594,
  					"duration": 210,
  					"price": 21
  				}
  			}, {
  				"start": "15:30",
  				"activity": {
  					"id": 6877,
  					"duration": 240,
  					"price": 106
  				}
  			}, {
  				"start": "20:00",
  				"activity": {
  					"id": 6865,
  					"duration": 30,
  					"price": 168
  				}
  			}]
  		}, {
  			"day": 2,
  			"itinerary": [{
  				"start": "10:00",
  				"activity": {
  					"id": 6647,
  					"duration": 30,
  					"price": 59
  				}
  			}, {
  				"start": "11:00",
  				"activity": {
  					"id": 6659,
  					"duration": 150,
  					"price": 33
  				}
  			}, {
  				"start": "14:00",
  				"activity": {
  					"id": 6616,
  					"duration": 180,
  					"price": 31
  				}
  			}, {
  				"start": "17:30",
  				"activity": {
  					"id": 6741,
  					"duration": 120,
  					"price": 94
  				}
  			}, {
  				"start": "20:00",
  				"activity": {
  					"id": 6779,
  					"duration": 90,
  					"price": 49
  				}
  			}]
  		}]
  	}
  }
  `
  
8. Solution might not be optimal but focus on the business requirements and the format of output.

## Ideas for improvement

###Greedy
1. Divide in price ranges - low, mid, high and in round robin manner choose activities

2. Start lowerPointer from start, higherPointer from end.
   Choose 2 lower activities per day and then 1 higher, until the time is over, keep filling the low cost activities

3. Add a priority weight factor (according to business requirements) and calculate each package score by time*price*priority
   Arrange in asc order of this factor and run the program


### Dynamic programming
 Memoization - 
   Store the responses for different query params in a db or cache
   If next request arrives with same params, just fetch the previous result and output

   Better than that, store packages in db for various budget ranges

   Eg: Under 100- 1day
       Over 100 - 1 day
       Over 200 - 1 day
       Under 200 - 2 day
       and so on...


When query arrives for example for budget = 1000 and days = 3
Check in db if this package is stored.
If not, check which is closest package stored in db for days = input days -1. Say it was package for budget 900 & days 2
Add the packages Ids to isVisitedArray.

Pick this package, and run the api I developed and run it for budget=100 and days= 1
So for last day populate the new packages returned by api which are not in isVisited array.
It will be faster since the program will run just for 1 day .




 
 