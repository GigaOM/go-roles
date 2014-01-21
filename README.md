go-roles
========

WordPress plugin to allow filterable definition of custom roles and their capabilities

Example of how to use this plugin to extend role definitions without adding them to the database:

```
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

