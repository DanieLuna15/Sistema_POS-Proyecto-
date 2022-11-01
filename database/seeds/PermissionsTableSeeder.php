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
                            'description'=>'Creación de categorias de productos en el sistema',
        ]);
        Permission::create(['name'=>'Visualización de detalle de Categoría',
                            'slug'=>'categories.show',
                            'description'=>'Visualización en detalle el listado de productos de cada categoría en el sistema',
        ]);
        Permission::create(['name'=>'Edición de Categorías',
                            'slug'=>'categories.edit',
                            'description'=>'Edición de cualquier dato de una categoría del sistema',
        ]);
        Permission::create(['name'=>'Eliminación de Categorías',
                            'slug'=>'categories.destroy',
                            'description'=>'Eliminación de una categoría del sistema',
        ]);
        // FIN CATEGORIAS
        //CATEGORIAS
        Permission::create(['name'=>'Navegación por Marcas',
                            'slug'=>'brands.index',
                            'description'=>'Listado y navegación por todas las marca de productos en el sistema',
        ]);
        Permission::create(['name'=>'Creación de Marcas',
                            'slug'=>'brands.create',
                            'description'=>'Creación de marca de productos en el sistema',
        ]);
        Permission::create(['name'=>'Visualización de detalle de marca',
                            'slug'=>'brands.show',
                            'description'=>'Visualización en detalle el listado de productos de cada marca en el sistema',
        ]);
        Permission::create(['name'=>'Edición de Marcas',
                            'slug'=>'brands.edit',
                            'description'=>'Edición de cualquier dato de una marca del sistema',
        ]);
        Permission::create(['name'=>'Eliminación de Marcas',
                            'slug'=>'brands.destroy',
                            'description'=>'Eliminación de una marca del sistema',
        ]);
        // FIN CATEGORIAS

        //PRODUCTOS
        Permission::create(['name'=>'Navegación por Productos',
                            'slug'=>'products.index',
                            'description'=>'Listado y navegación por todas los productos en el sistema',
        ]);
        Permission::create(['name'=>'Creación de Productos',
                            'slug'=>'products.create',
                            'description'=>'Creación de productos en el sistema',
        ]);
        Permission::create(['name'=>'Visualización de detalle de Categoría',
                            'slug'=>'products.show',
                            'description'=>'Visualización en detalle los productos en el sistema',
        ]);
        Permission::create(['name'=>'Edición de Productos',
                            'slug'=>'products.edit',
                            'description'=>'Edición de cualquier dato de un producto del sistema',
        ]);
        Permission::create(['name'=>'Eliminación de Productos',
                            'slug'=>'products.destroy',
                            'description'=>'Eliminación de un producto del sistema',
        ]);
        Permission::create(['name'=>'Cambiar Estado de Producto',
                            'slug'=>'change.status.products',
                            'description'=>'Permite el cambio de estado de un producto',
        ]);
        // FIN PRODUCTOS

        //USUARIOS
        Permission::create(['name'=>'Navegación por Usuarios',
                            'slug'=>'users.index',
                            'description'=>'Listado y navegación por todos los registrados usuario en el sistema',
        ]);
        Permission::create(['name'=>'Creación de Usuarios',
                            'slug'=>'users.create',
                            'description'=>'Creación de usuarios en el sistema',
        ]);
        Permission::create(['name'=>'Visualización de detalle de marca',
                            'slug'=>'users.show',
                            'description'=>'Visualización en detalle cada usuario del sistema',
        ]);
        Permission::create(['name'=>'Edición de Usuarios',
                            'slug'=>'users.edit',
                            'description'=>'Edición de cualquier dato de usuarios del sistema',
        ]);
        Permission::create(['name'=>'Eliminación de Usuarios',
                            'slug'=>'users.destroy',
                            'description'=>'Eliminación de un usuario del sistema',
        ]);
        Permission::create(['name'=>'Cambiar Estado de Usuario',
                            'slug'=>'change.status.users',
                            'description'=>'Permite el cambio de estado de un usuario',
        ]);
        // FIN USUARIOS

        //ROLES
        Permission::create(['name'=>'Navegación por Roles',
                            'slug'=>'roles.index',
                            'description'=>'Listado y navegación por todos los roles del sistema',
        ]);
        Permission::create(['name'=>'Creación de Roles',
                            'slug'=>'roles.create',
                            'description'=>'Acceder a la vista de Creación de roles en el sistema',
        ]);
        Permission::create(['name'=>'Visualización de detalle de Rol',
                            'slug'=>'roles.show',
                            'description'=>'Visualización en detalle el listado de de roles de usuarios en el sistema',
        ]);
        Permission::create(['name'=>'Edición de Roles',
                            'slug'=>'roles.edit',
                            'description'=>'Edición de cualquier dato de un rol del sistema',
        ]);
        Permission::create(['name'=>'Eliminación de Roles',
                            'slug'=>'roles.destroy',
                            'description'=>'Eliminación de un rol del sistema',
        ]);
        // FIN CATEGORIAS
    }
}
