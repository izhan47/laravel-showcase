# Wagenabled

Wagenabled is a website. This Business website.

## Notes for Project

### Important Rules for the Projects:

- User platform:
    - Registration ( Manually, Google)
    - Login ( Manually, Google)
    - Forgot Password
    - Send business request 
    - Contact Module
        - send message 

    - Profile module
        - Update profile
        - Update vet information
        - Add, Remove Pets
        - Listing pets
        - Listing liked pet pros, dislike pet pros
        - Listing liked watch and learn 
        - Listing Saved Product Reviews
        - Listing reviews of pet pros

    - Pet pro module
        - Pet pro Listing ( Sort By Latest, popular, deal offered, nearest  / Filter by Category / Filter by location / Search Keyword / Map view )
        - Details Pet pro 
            - View pet pro details, gallery
            - Like Pet pro 
            - Post review ( Give Rate)
            - View Deal
            - Claim Deal
            - View Event

    - Watch and learn module            
        - Watch and learn Listing ( Sort By Latest, popular / Filter by Category / Search )
        - Details Watch and learn 
            - View Watch and learn details
            - Like Watch and learn 
            - Post comment, delete posted comment, reply comment
            - View author
            - listing related watch and learn 

    - Product Reviews module            
        - Product reviews Listing ( Sort By Latest, popular / Filter by Category / Search )
        - Details product reviews 
            - View product reviews details
            - Like product reviews 
            - Post comment, delete posted comment, reply comment
            - View author
            - listing related product reviews 


- Admin has capabilities of: 
    - User's chart and view count of users, pet pros, deals of watch and learn, deal claimed of watch and learn, deals of product reviews, deal claimed of product reviews
    - Add / Update / Delete Admin User
    - Listing / View details/ Delete user 

    - Listing / Add/ Update/ Delete Pet pro category
    - Listing / Add/ Update/ Delete Pet pro
        - Listing / Add/ Update/ Delete gallery images, make image as cover image
        - Listing / Add/ Update/ Delete deals
        - Listing / Add/ Update/ Delete events

    - Listing / Add/ Update/ Delete watch and learn category
    - Listing / Add/ Update/ Delete / View / Change status / watch and learn  
        -  Manage watch and learn using content builder custom design

    - Listing / Add/ Update/ Delete product reviews category
    - Listing / Add/ Update/ Delete / View / Change status / product reviews  
        -  Manage product reviews using content builder custom design

    - Listing / Add/ Update/ Delete Authors
    - Listing / Add/ Delete Medias 

    - Listing / Add/ Update/ Delete testimonial
    - Listing / View / Delete Business Requests
    - Listing / Delete Business Requests
    - Listing / View contact
    - Listing / Delete Newsletters

### Tech Stacks & Version

- PHP 7.2.5
- Laravel 7.0
- React 16.13.1
- MySQL 5.7.27
- jQuery
- JavaScript
- GitHub

### Technology 
- Front End: React Js
- Back End: Laravel 

#### Server:
- Amazon AWS EC2 (Hosting)
- AWS Route53 (DNS Management)

#### External Tools:
- Facebook Login / Singup
- Google Login / Singup
- Hubspot
- Google analytics
- Google Map

## Getting Started: Setting Up Local Environment & Prerequisites

#### GitHub Repo Structure:
1. Master (Connected with Live Server)
2. Develop (Connected with Dev Server)
3. Branch from Develop (Task wise branch)

#### To setup in local get latest code: 

Checkout develop repo in local from git. 

#### Prerequisites:

- Composer
- Node
- MySQL
- Apache
- PHP >= 7.1.3

#### Setup Steps:

1. `composer install`

2. ` npm install`

3. Copy .env.example file to create .env file and setup configuration for database.

4. `php artisan serve `

To make changes and work on tasks: 
Create branch for particular task and start working on your changes.

## Deployment steps

- #### Create pull request to parent repo (Develop/Master). 
    Merge PR to parent repo. 

- #### Develop/Staging Server Deployment:

    Command to update server with changes which are on develop branch

    `git pull `
- #### Live server Deployment:

    Command to update server with changes which are on master branch

     `git pull `
- #### Branch Process:

    Task branch/Child branch -> Develop Branch -> Master Branch
- #### If there is any urgent task in live server:

    Create branch from master and work on that child branch. Merge your changes to Master branch. and deploy to live server.

    Merge that child branch -> Develop branch
