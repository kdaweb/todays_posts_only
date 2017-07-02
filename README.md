# todays_posts_only
## Introduction
This is a WordPress plugin that adds a function and shortcode to display posts
rom a given day (by default, today) only.  Additional filters may be provided
to restrict returned posts by category and/or by tag.

## Parameters
### date
This is a string representing the date for posts returned by the query.  

The string is parsed by PHP's parse_date function:

http://php.net/manual/en/function.date-parse.php

As a result, 'date' can be passed strings such as "today" or "yesterday" or "2017-07-01".

The default for 'date' is the string "today"; this is how todays_posts_only returns... today's posts only.

### category
This is a string that filters out posts that aren't in this category.  This filter is based on the name of the category, not the id.  If no category is provided, no filtering based on category will be performed.

### tag
This is a string that filters out posts that don't have this tag.  If no tag is provided, no filtering will be performed on the posts' tags.

### template_part
This is the slug for the part of the template to be included; it corresponds to the "slug" parameter to the get_template_part() function.

https://developer.wordpress.org/reference/functions/get_template_part/

The default value is 'content'.

For reference, the "name" parameter for get_template_part() is the format of the post being included.

## Examples

### Example 1

[todays_posts_only]

This will include posts from only the current day, regardless of category or tag.  The posts are rendered with the theme's "content" template.

This is the same as:

[todays_posts_only date="today"]

### Example 2

[todays_posts_only date="yesterday"]

This will include posts posted the day before; again, no filtering based on tag or category will be performed and the "content" template will be used.

### Example 3

[todays_posts_only category="News"]

This will include posts posted on the current day, but only if they are in the "?News" category.

### Example 4

[todays_posts_only tag="PHP" date="7/14/2017"]

This will display only posts with the "PHP" tag that were posted on July 14, 2017.


