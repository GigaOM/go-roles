go-roles
========

WordPress plugin to allow filterable definition of custom roles and their capabilities

Example of how to use this plugin to extend role definitions without adding them to the database:

```php
add_filter( 'go_roles', 'gigaom_filter_user_roles' );
function gigaom_filter_user_roles( $roles ) {
    $roles['subscriber-lifetime'] => array(
        'name' => 'Subscriber, Lifetime',
        'extends' => 'subscriber',
        'add_caps' => ( 'read_private_posts' ), // optional
        'remove_caps' => ( 'read' ), // optional (and a silly example)
    );
 
    return $roles;
}
```

For more back story on why we created this plugin and how it can be used, check out our article:

[Gigaom Kitchen: Dynamically add roles to WordPress](http://kitchen.gigaom.com/2014/01/21/dynamically-add-roles-to-wordpress/)
