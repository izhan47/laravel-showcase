# Laravel Project

This Laravel Project is a business website built with Laravel and React, featuring a variety of modules and functionalities for users, pet professionals, and administrators.

## Tech Stacks & Versions

- PHP 7.2.5
- Laravel 7.0
- React 16.13.1
- MySQL 5.7.27
- jQuery
- JavaScript
- GitHub

## Technology

- **Front End:** React Js
- **Back End:** Laravel

## Server

- **Amazon AWS EC2 (Hosting)**
- **AWS Route53 (DNS Management)**

## External Tools

- Facebook Login / Signup
- Google Login / Signup
- Hubspot
- Google Analytics
- Google Map

## Getting Started: Setting Up Local Environment & Prerequisites

### GitHub Repo Structure

- **Master:** Connected with Live Server
- **Develop:** Connected with Dev Server
- **Branch from Develop:** Task-wise branch

### To set up locally:

1. Clone the develop branch from the GitHub repository.
2. Install prerequisites: Composer, Node, MySQL, Apache, PHP >= 7.1.3.

### Setup Steps

```bash
composer install
npm install
```

3. Copy `.env.example` to create a `.env` file and configure the database settings.
4. Run the development server:

```bash
php artisan serve
```

### Making Changes and Working on Tasks

1. Create a branch for a specific task.
2. Make changes and work on the task.
3. Create a pull request to the parent repo (Develop/Master).

### Deployment Steps

#### Develop/Staging Server Deployment:

```bash
# Update server with changes on develop branch
git pull origin develop
```

#### Live Server Deployment:

```bash
# Update server with changes on master branch
git pull origin master
```

### Branch Process

- Task branch/Child branch -> Develop Branch -> Master Branch

### Urgent Task on Live Server

1. Create a branch from master for the urgent task.
2. Merge changes to the Master branch and deploy to the live server.
3. Merge the child branch into the Develop branch.

## Admin Capabilities

- User's chart and view count of users, pet pros, deals of watch and learn, deal claimed of watch and learn, deals of product reviews, deal claimed of product reviews.
- Add / Update / Delete Admin User.
- Manage Pet Pro categories, Pet Pros, gallery images, deals, and events.
- Manage Watch and Learn categories and content.
- Manage Product Reviews categories and content.
- Manage Authors, Medias, Testimonials.
- View and manage Business Requests, Contact, and Newsletters.

## Developer Information

- **Developer Email:** izhan47@gmail.com
- **Developer Website/Support:** [izhan.me](https://izhan.me)

## Main Gif Path

- **png:** [demo.png](./path/to/demo.png)
