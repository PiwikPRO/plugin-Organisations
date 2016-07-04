# Piwik Organisations Plugin

## Description

This plugin allows to associate visitors with configurable organisations based on their IP. 
(Or you may use it to build other named groups based on the users IP.)

Organisations can be defined piwik wide within the admin ui. Each organisation will be identified by an id and will contain a name and ip ranges.
Configured IP ranges can't overlap as they will be checked before saving them.

Whenever a new visit will be tracked, the users IP will be used to check if one of the configured IP ranges is matching.
This plugin respects Piwiks privacy configuration. If you configured your Piwik to anonymise the IP before processing IP based data, this plugin might not be able to identify the organisations correctly. 
To speed up the detection the IP ranges are cached in a better processable format. This cache will be cleared at least once a day, as soon as a change was made. Thus changes will take effect at least after a day.


### Requirements

[Piwik](https://github.com/piwik/piwik) 2.16.0 or higher is required.

### Features

- Manage ([CRUD](https://en.wikipedia.org/wiki/Create,_read,_update_and_delete)) organisation information (name / IP ranges)
- View reports based on organisation data (including goal metrics)
- Possibility to create segments based on an organisation
- Possibility to force organisation in tracking request using `idorg` param

## Changelog

- 1.0 Initial release

