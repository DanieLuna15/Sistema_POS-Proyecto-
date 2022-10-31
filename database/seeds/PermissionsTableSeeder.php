<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        //CATEGORIAS
        Permission::create(['name'=>'Navegación por Categorias',
                            'slug'=>'categories.index',
                            'description'=>'Listado y navegación por todas las categorias de productos en el sistema',
        ]);
        Permission::create(['name'=>'Creación de Categorías',
                            'slug'=>'categories.create',
                            'description'=>'Acceder a la vista de Creación de categorias de productos en el sistema',
        ]);
        Permission::create(['name'=>'Registro Categorías',
                            'slug'=>'categories.store',
                            'description'=>'Registro y almacenamiento de categorias de productos en el sistema',
        ]);
        Permission::create(['name'=>'Visualización de detalle de Categoría',
                            'slug'=>'categories.show',
                            'description'=>'Visualización en detalle el listado de productos de cada categoría en el sistema',
        ]);
        Permission::create(['name'=>'Edición de Categorías',
                            'slug'=>'categories.edit',
                            'description'=>'Edición de cualquier dato de una categoría del sistema',
        ]);
        Permission::create(['name'=>'Modificación de Categorías',
                            'slug'=>'categories.update',
                            'description'=>'Modificación de cualquier dato de una categoría del sistema',
        ]);
        Permission::create(['name'=>'Eliminación de Categorías',
                            'slug'=>'categories.destroy',
                            'description'=>'Eliminación de una categoría del sistema',
        ]);;
        // FIN CATEGORIAS
        //CATEGORIAS
        Permission::create(['name'=>'Navegación por Marcas',
                            'slug'=>'brands.index',
                            'description'=>'Listado y navegación por todas las marca de productos en el sistema',
        ]);
        Permission::create(['name'=>'Creación de Marcas',
                            'slug'=>'brands.create',
                            'description'=>'Acceder a la vista de Creación de marca de productos en el sistema',
        ]);
        Permission::create(['name'=>'Registro Marcas',
                            'slug'=>'brands.store',
                            'description'=>'Registro y almacenamiento de marca de productos en el sistema',
        ]);
        Permission::create(['name'=>'Visualización de detalle de marca',
                            'slug'=>'brands.show',
                            'description'=>'Visualización en detalle el listado de productos de cada marca en el sistema',
        ]);
        Permission::create(['name'=>'Edición de Marcas',
                            'slug'=>'brands.edit',
                            'description'=>'Edición de cualquier dato de una marca del sistema',
        ]);
        Permission::create(['name'=>'Modificación de Marcas',
                            'slug'=>'brands.update',
                            'description'=>'Modificación de cualquier dato de una marca del sistema',
        ]);
        Permission::create(['name'=>'Eliminación de Marcas',
                            'slug'=>'brands.destroy',
                            'description'=>'Eliminación de una marca del sistema',
        ]);;
        // FIN CATEGORIAS
    }
}
