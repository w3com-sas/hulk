# W3COM  : Hulk - Hana Utils Kit 

This bundle provides ways to generate DataTables from Json file,
with function for several or single data and with filters signle or multiple.

## Installation

#### Download the bundle

Use SATIS to get the bundle through composer by modifying your `composer.json` :

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://satis.w3cloud.fr"
        }
    ]
}
```

Also add the dependency :

```json
{
"require": {
        "w3com-sas/hulk": "^1.0"
    }
}
```

#### Configure the bundle

Add the directory of json files in config/package/hulk.yaml

````yaml
w3com_hulk:
  json_display:
    url_files: '/project/Display/'
````

Declare routes in config/route.yaml
 
````yaml
display_table:
  resource: '@W3comHulkBundle/Resources/config/routing/display_table_view.xml'

create_view:
  resource: '@W3comHulkBundle/Resources/config/routing/create_view.xml'

update_view:
  resource: '@W3comHulkBundle/Resources/config/routing/update_view.xml'
  
update_entity:
  resource: '@W3comHulkBundle/Resources/config/routing/update_entity.xml'
```` 

[Next step, create the json file](Resources/doc/1_create_json_file.rst)




