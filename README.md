# Events Management Module

## Overview

The Events Management module allows back-end users to manage events and end-users to browse them. The module includes CRUD operations for events, configuration options, and a front-end display of events.

## Features

- **Event Attributes**: Title, Image, Description, Start Time, End Time, Category.
- **Back-end**:
  - CRUD operations for managing events.
  - Option to show or hide past events.
  - Control the number of events listed on the listing page.
  - Custom database table to log configuration changes.
- **Front-end**:
  - Page to list published events.
  - Details page for individual events.
  - Drupal block to list the latest 5 created events.

## Prerequisites

- **Drupal**: Version 10
- **PHP**: >= 8.2
- **MySQL**: >= 8.0
- **Docker** (optional): For setting up the development environment.

## Installation Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/Messelhy32/drupal.git drupal-project
cd drupal-project
```

### 2. Set Up Docker Environment (Optional)

If you are using Docker, start the environment with Drupal and MySQL:

```bash
docker-compose up -d
```

### 3. Install Drupal (If not already installed)

If you're setting up a fresh instance of Drupal:

- Run through the Drupal installation process via the web interface at `http://localhost:8080`.
- Ensure that the database and site configurations are properly set up.

### 4. Enable the Module

Enable the Events Management module using Drush:

```bash
cd drupal
drush en event_management -y
```

### 5. Configuration

Navigate to **Configuration** > **Event Management Settings** to configure the module:

- Option to show or hide past events.
- Set the number of events to display on the listing page.

### 6. Viewing Events

- Go to `/events` to see the list of published events.
- Click on any event to view its details.
- Display the latest 5 events using the "Latest Events Block".

### 7. Custom Database Table

The module creates a custom database table to log changes made to the module's configuration.

### 8. Testing

- Use the provided form at `/admin/content/event/add` to add events.
- Test the configuration options and event display as needed.

## Git Repository Structure

- **drupal/**: Contains all Drupal setup and custom modules including the "Events Management" module.
- **docker-compose.yml**: Docker configuration for setting up the environment.
