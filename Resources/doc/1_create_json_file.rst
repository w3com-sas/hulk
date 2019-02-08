Create the json file
=============================

A) Calculation view
--------------------


.. code:: json

    {
        "CalculationView" : "ExempleView"
    }

The bundle find all calculation views that they're exposed in "service.xsodata" file.


B) Global actions
-----------------

Global actions are defined to manage multiple rows of the DataTable. It's an array so it can contain
lot of actions. In the front of the application, these appear when user click on "Action" link.

The global actions section must look like the following code :

.. code:: json

    {
        "GlobalActions": [
            {
                "Label": "First action",
                "Type": "exemple-type"
            },
            {
                "Label": "Second action",
                "Type": "exemple-type"
            }
        ],
    }


Different type who exist :

Excel export
~~~~~~~~~~~~

.. code:: json


    {
        "Label": "Export excel",
        "Type": "export-csv"
    }

Update SAP
~~~~~~~~~~

The aim of this function is to update many data in SAP in one time. It's possible to manage the type of the target data.
For exemple, some lines need to pass under the "ARCHIVE" status, so you can create a global action who the targetData
is "ARCHIVE". So we give a type for the target data. You can see code below.

.. code:: json


    {
        "Label": "Static",
        "Type": "update-sap",
        "Config" : {
            "Entity": "BusinessPartner",
            "EntityColumnKey": "CardCode",
            "TargetField": "CardName",
            "TargetDataType": "static",
            "TargetData": "NewCardName"
        }
    }


The others fields help the app to find concerned entities and update them. We just saw the STATIC type,
now we will see the TEXT type.
The TEXT type allow the user of the web-app to type what he want. It generate an basic input text. So
it doesn't need a targetData because the user chose it. See the code below

.. code:: json


    {
        "Label": "Static",
        "Type": "update-sap",
        "Config" : {
            "Entity": "BusinessPartner",
            "EntityColumnKey": "CardCode",
            "TargetField": "CardName",
            "TargetDataType": "text",
        }
    }


The last type of targetDataType is the array. The array provide list of choice and the web-app user
can choice one of them. We need you to provid a list of choices, give us an array with list of choices
key-value.

.. code:: json


    {
        "Label": "Array",
        "Type": "update-sap",
        "Config" : {
        "Entity": "BusinessPartner",
            "EntityColumnKey": "CardCode",
            "TargetField": "CardName",
            "TargetDataType": "array",
            "TargetData": {
                "forSale" : "À vendre",
                "sold" : "Vendu"
            }
        }
    }


In this exemple, the web-app user see in the screen "À vendre" and the data that will be sent is "forSale".


C) Filters
----------

Filters are defined to sort out data in the front of the application. These appear when user click on "Action" link.
Actually there are 2 type of filter. The "single" type, who is related to the data of the concerned field,
and the "multiple" type who is not related to the data. It's also an array in the json file.

The filters section must look like the following code :

.. code:: json

    {
        "Filters":  [
            {
                "FieldName": "U_W3C_EXEMPLE",
                "Label": "Exemple de nom :",
                "Type": "exemple-type"
            },
            {
                "FieldName": "U_W3C_EXEMPLE2",
                "Label": "Exemple de nom 2 :",
                "Type": "exemple-type-2"
            }
        ]
    }

Single type
~~~~~~~~~~~

The single type get all different data of the concerned field. For exemple in a field who have
3 different status, the user can select 4 different choices in front of the application.
Nothing, or one of the 3 status. You don't need to add a columns related to the filter, this
will be automatically done.

.. code:: json


    {
        "FieldName": "U_W3C_FIELD_STATUS",
        "Label": "Exemple de nom :",
        "Type": "single"
    }



Multiple type
~~~~~~~~~~~~~

The multiple type must be related to integer or float field. Actually he can't find an date interval.
And the system can support only one multiple filter. Soon it will be possible to add more multiple
filter.

.. code:: json

    {
        "FieldName": "U_W3C_PRICE",
        "Label": "Interval de prix :",
        "Type": "multiple"
    }


Multiple type date
~~~~~~~~~~~~~~~~~~

If we need to provid date interval filter

.. code:: json

    {
        "FieldName": "U_W3C_DATE",
        "Label": "Interval des dates :",
        "Type": "multiple-data"
    }


C) Columns
----------

Columns are defined to see them in the final table of the application. Columns can contain action related to the
cell or the lines of the table. Actually it exists 3 types of column.

.. code:: json

    {
        "Columns":[
            {
                "Label" : "Nom partenaire : ",
                "FieldName" : "CardName",
                "Type" : "text"
            }
        ]
    }


Text type
~~~~~~~~~~

Text is the most simple type of column. Is just the raw data in the view.

It look like this :

.. code:: json


    {
        "Label" : "Nom partenaire : ",
        "FieldName" : "CardName",
        "Type" : "text"
    }



Action type
~~~~~~~~~~~

The action's column is call "CellAction", because it is related to the cell. The main of the cell action
it's to provide a bridge between SAP and the application. They're several function related to the CellAction.

Action type : open form in SAP
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

It's important to know the "FieldName" of the column must be the ID of the "TargetEntity". In this exemple,
the "CardCode" is the id of the entity "OCRD". The icon will appear on the button related to this action. For
icons, the library used to run it is https://fontawesome.com/

.. code:: json

    {
        "Label" : "Préférence",
        "FieldName" : "CardCode",
        "Type" : "action",
        "CellAction" : {
            "FunctionName": "openForm",
            "TargetEntity": "OCRD",
            "Icon" : "user-alt"
        }
    }

.. _`fontawesome`:
