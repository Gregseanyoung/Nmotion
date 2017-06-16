<?php

namespace Codeception\Module;

use \Codeception\Module;

class ApiHelper extends Module
{
    private function coalesce($arg1)
    {
        foreach (func_get_args() as $arg) {
            if ($arg !== null) {
                return $arg;
            }
        }

        return null;
    }

    private function getAuthToken($email, $password, $device_id, $salt)
    {
        return $email .'|'. md5($password);
    }

    private function persistEntity($entityName, $entityData)
    {
        $tableName = $entityName;
        $data      = $entityData;

        /** @var $db \PDO */
        $db = $this->getModule('Db')->driver->getDbh();

        $query = $db->prepare("DESCRIBE $tableName");
        $query->execute();
        $tableFields = $query->fetchAll($db::FETCH_COLUMN);

        $fieldNames = $fieldPlaceholders = $statementParameters = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $tableFields)) {
                $fieldNames[]              = '`' . $key . '`';
                $fieldPlaceholders[]       = ':' . $key;
                $statementParameters[$key] = is_bool($value) ? (int)$value : $value;
            }
        }

        $sql = sprintf(
            'INSERT INTO `%s`(%s) VALUES (%s)',
            $tableName,
            implode(',', $fieldNames),
            implode(',', $fieldPlaceholders)
        );

        $this->debugSection('Query', $sql . "\n" . json_encode($statementParameters));
        $statement = $db->prepare($sql);

        if (! $statement) {
            $this->fail("Query '$sql' can't be executed.");
        }

        $result = $statement->execute($statementParameters);

        if (! $result) {
            $this->fail(implode($statement->errorInfo()));
        }

        return $db->lastInsertId();
    }

    private function updateEntity($entityName, $id, $entityData)
    {
        $tableName = $entityName;
        $data      = $entityData;

        /** @var $db \PDO */
        $db = $this->getModule('Db')->driver->getDbh();

        $query = $db->prepare("DESCRIBE $tableName");
        $query->execute();
        $tableFields = $query->fetchAll($db::FETCH_COLUMN);

        $awarding            = [];
        $statementParameters = ['id' => $id];

        foreach ($data as $key => $value) {
            if (in_array($key, $tableFields)) {
                $awarding[] = sprintf('`%1$s`= :%1$s', $key);
                $statementParameters[$key] = is_bool($value) ? (int)$value : $value;
            }
        }

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE id = :id',
            $tableName,
            join(', ', $awarding)
        );

        $this->debugSection('Query', $sql . "\n" . json_encode($statementParameters));
        $statement = $db->prepare($sql);

        if (!$statement) {
            $this->fail("Query '$sql' can't be executed.");
        }

        $result = $statement->execute($statementParameters);

        if (!$result) {
            $this->fail(implode($statement->errorInfo()));
        }
    }

    /**
     * @param array $entity
     * @return array
     */
    private function mapEntityDataToFormData($entity)
    {
        $mappedEntity = [];
        foreach ($entity as $name => $value) {
            $mappedName = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
            $mappedEntity[$mappedName] = $value;
        }

        return $mappedEntity;
    }

    private function getNextDefaultRestaurantName()
    {
        static $i = 0;
        return 'Test Restaurant ' . $i++;
    }

    private function getNextDefaultEmail()
    {
        static $i = 0;
        return 'test.' . ($i++) . '@nmotio.pp.ciklum.com';
    }

    private function assertResponseContainsNumberOfValuesForEntry($json, $count)
    {
        /** @var $rest REST */
        $rest = $this->getModule('REST');
        $data = $rest->grabDataFromJsonResponse($json);
        \PHPUnit_Framework_Assert::assertEquals(
            $count,
            count($data),
            sprintf("JSON response does not contain %s entry(s) for '%s'", $count, $json)
        );
    }

    public function willEvaluateAuthorizationToken($email, $password, $device_id = '', $tokenName = 'NmotionToken')
    {
        /** @var $rest REST */
        $rest = $this->getModule('REST');
        $authToken = $this->getAuthToken($email, $password, $device_id, '');
        $this->debugSection('AuthHeader', 'Auth: ' . $tokenName . ' ' . $authToken);
        $rest->haveHttpHeader('Auth', $tokenName . ' ' . $authToken);
    }

    public function addRestaurantFixture(array $params = [])
    {
        try {
            if (! array_key_exists('adminUser', $params)) {
                $params['adminUser'] = [];
            }

            if ($params['adminUser'] !== null) {
                if (!isset($params['adminUser']['roles'])) {
                    $params['adminUser']['roles'] = [
                        'ROLE_RESTAURANT_GUEST',
                        'ROLE_RESTAURANT_STAFF',
                        'ROLE_RESTAURANT_ADMIN'
                    ];
                }

                $adminUser = $this->addUserFixture($params['adminUser']);
                $params['admin_user_id'] = $adminUser['id'];
            }

            if (! array_key_exists('address', $params)) {
                $params['address'] = [];
            }

            if ($params['address'] !== null) {
                $address = $this->addRestaurantAddressFixture($params['address']);
                $params['restaurant_address_id'] = $address['id'];
            }

            if (isset($params['logoAsset'])) {
                $params['logoAsset'] = $params['logoAsset'] + ['name' => 'Restaurant logo asset'];
                $logoAsset = $this->addAssetFixture($params['logoAsset']);
                $params['logo_asset_id'] = $logoAsset['id'];
            }

            $defaults = [
                'name'                 => $this->getNextDefaultRestaurantName(),
                'full_description'     => 'test test full test description',
                'email'                => $this->getNextDefaultEmail(),
                'phone'                => '+123456789',
                'site_url'             => 'http://www.ciklum.com',
                'feedback_url'         => 'http://www.ciklum.com',
                'video_url'            => 'http://www.ciklum.com',
                'contact_person_name'  => 'Foo',
                'contact_person_phone' => '+123456789',
                'contact_person_email' => 'test@nmotion.ciklum.com',
                'visible'              => '0',
                'ta_member'            => 0,
                'in_house'             => 1,
                'takeaway'             => 0,
                'room_service'         => 0,
                'check_out_time'       => 60,
                'invoicing_period'     => 'monthly',
                'vat_no'               => 12345678,
                'reg_no'               => 1234,
                'konto_no'             => 12,
                'created_at'           => time(),
            ];

            $restaurantParams       = $params + $defaults;
            $restaurantParams['id'] = $this->persistEntity('nmtn_restaurant', $restaurantParams);

            $restaurant = $this->mapEntityDataToFormData($restaurantParams);

            if (isset($adminUser)) {
                $restaurant['adminUser'] = $adminUser;
            }

            if (isset($address)) {
                $restaurant['address'] = $address;
            }

            if (isset($params['menuCategories']) && is_array($params['menuCategories'])) {
                $position = 0;
                $restaurant['menuCategories'] = $params['menuCategories'];
                foreach ($restaurant['menuCategories'] as &$category) {
                    $category['restaurant_id'] = $restaurant['id'];
                    $category['position']      = $position++;
                    $category = $this->addMenuCategoryFixture($category);
                }
            }

            if (! array_key_exists('operationTimes', $params)) {
                $params['operationTimes'] = array_fill(0, 7, []);
            }

            $restaurant['operationTimes'] = $params['operationTimes'];

            foreach ($restaurant['operationTimes'] as &$operationTime) {
                $operationTime['restaurant_id'] = $restaurant['id'];
                $operationTime = $this->addRestaurantOperationTimeFixture($operationTime);
            }

            return $restaurant;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addRestaurantAddressFixture(array $params = [])
    {
        try {
            $defaults = [
                'address_line1' => 'some test address',
                'city'          => 'Kyiv',
                'postal_code'   => '00000',
                'country_id'    => 1,
                'latitude'      => 50.0,
                'longitude'     => 30.4
            ];

            $addressParams       = $params + $defaults;
            $addressParams['id'] = $this->persistEntity('nmtn_restaurant_address', $addressParams);

            return $this->mapEntityDataToFormData($addressParams);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addUserFixture(array $params = [])
    {
        try {
            $defaults = [
                'email'               => $this->getNextDefaultEmail(),
                'first_name'          => 'Foo',
                'last_name'           => 'Bar',
                'registered'          => true,
                'registration_origin' => 'Nmotion',
                'enabled'             => true,
                'salt'                => md5(time()),
                'password'            => 'qwertyui',
                'locked'              => 0,
                'expired'             => 0,
                'credentials_expired' => 0,
                'role'                => 'ROLE_RESTAURANT_GUEST',
                'roles'               => ['ROLE_RESTAURANT_GUEST'],
                'created_at'          => time(),
            ];

            if (! array_key_exists('role', $params) && array_key_exists('roles', $params)) {
                foreach (['ROLE_SOLUTION_ADMIN', 'ROLE_RESTAURANT_ADMIN', 'ROLE_RESTAURANT_STAFF'] as $role) {
                    if (in_array($role, $params['roles'])) {
                        $params['role'] = $role;
                        break;
                    }
                }
            }

            $userParams                       = $params + $defaults;
            $userParams['email_canonical']    = $userParams['email'];
            $userParams['username']           = $userParams['email'];
            $userParams['username_canonical'] = $userParams['username'];

            $persistParams             = $userParams;
            $persistParams['password'] = md5($persistParams['password']);
            $persistParams['roles']    = serialize(array_unique($userParams['roles']));

            $userParams['id'] = $this->persistEntity('nmtn_user', $persistParams);

            return $this->mapEntityDataToFormData($userParams);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addAnonymousUserFixture(array $params = [])
    {
        try {
            $deviceIdentity = md5(microtime());
            $defaults       = [
                'device_identity' => $deviceIdentity,
                'email'           => $deviceIdentity,
                'username'        => $deviceIdentity,
                'first_name'      => 'anonymous',
                'last_name'       => 'anonymous',
                'role'            => 'ROLE_RESTAURANT_GUEST',
                'roles'           => ['ROLE_RESTAURANT_GUEST'],
                'registered'      => false,
                'enabled'         => true
            ];

            $persistParams = $params + $defaults;

            $user = $this->addUserFixture($persistParams);

            $persistParams['user_id'] = $user['id'];

            $this->persistEntity('nmtn_user_device', $persistParams);

            return $user;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addRestaurantStaffFixture($restaurantId, array $params = [])
    {
        $params['roles'] = [
            'ROLE_RESTAURANT_GUEST',
            'ROLE_RESTAURANT_STAFF'
        ];

        $user = $this->addUserFixture($params);

        $persistParams = [
            'restaurant_id' => $restaurantId,
            'user_id'       => $user['id'],
        ];
        $this->persistEntity('nmtn_restaurant_staff', $persistParams);

        return $user;
    }

    public function addMenuCategoryFixture(array $params = [])
    {
        try {
            $defaults = [
                'name'       => 'Test Menu Category',
                'time_from'  => 0,
                'time_to'    => 86399,
                'discount_percent' => 0,
                'visible'    => 1,
                'position'   => 1,
                'created_at' => time(),
            ];

            $menuCategoryParams       = $params + $defaults;
            $menuCategoryParams['id'] = $this->persistEntity('nmtn_menu_category', $menuCategoryParams);

            $category = $this->mapEntityDataToFormData($menuCategoryParams);

            if (isset($params['menuMeals']) && is_array($params['menuMeals'])) {
                $position = 0;
                $category['menuMeals'] = $menuCategoryParams['menuMeals'];
                foreach ($category['menuMeals'] as &$meal) {
                    $meal['restaurant_id']               = $category['restaurantId'];
                    $meal['menu_category_id'] = $category['id'];
                    $meal['position'] = $position++;
                    $meal = $this->addMealFixture($meal);
                }
            }

            return $category;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addMealFixture(array $params = [])
    {
        try {
            $defaults = [
                'logo_asset_id' => null,
                'name'          => 'Test Menu Meal',
                'description'   => 'Test Menu Description',
                'price'         => 1.0,
                'discount_percent' => 0,
                'visible'       => 1,
                'time_from'     => 0,
                'time_to'       => 0,
                'position'      => 0,
                'meal_option_default_id' => null,
                'created_at'    => time(),
            ];

            $menuMealParams = $params + $defaults;

            if (isset($menuMealParams['logoAsset'])) {
                $menuMealParams['logoAsset'] = $menuMealParams['logoAsset'] + ['name' => 'Meal logo asset'];
                $logoAsset = $this->addAssetFixture($menuMealParams['logoAsset']);
                $menuMealParams['logo_asset_id'] = $logoAsset['id'];
            }

            if (isset($menuMealParams['thumbLogoAsset'])) {
                $menuMealParams['thumbLogoAsset'] =
                    $menuMealParams['thumbLogoAsset'] + ['name' => 'Thumb Meal logo asset'];
                $thumbLogoAsset = $this->addAssetFixture($menuMealParams['thumbLogoAsset']);
                $menuMealParams['thumb_logo_asset_id'] = $thumbLogoAsset['id'];
            }

            $menuMealParams['id'] = $this->persistEntity('nmtn_meal', $menuMealParams);

            $meal = $this->mapEntityDataToFormData($menuMealParams);

            if (isset($params['mealOptions']) && is_array($params['mealOptions'])) {
                $meal['mealOptions'] = $menuMealParams['mealOptions'];
                foreach ($meal['mealOptions'] as &$mealOption) {
                    $mealOption['meal_id'] = $meal['id'];
                    $mealOption = $this->addMealOptionFixture($mealOption);

                    if (isset($mealOption['isDefault']) && $mealOption['isDefault']) {
                        $this->updateEntity(
                            'nmtn_meal',
                            $meal['id'],
                            ['meal_option_default_id' => $mealOption['id']]
                        );
                    }
                }
            }

            if (isset($params['mealExtraIngredients']) && is_array($params['mealExtraIngredients'])) {
                $meal['mealExtraIngredients'] = $params['mealExtraIngredients'];
                foreach ($meal['mealExtraIngredients'] as &$mealExtraIngredient) {
                    $mealExtraIngredient['meal_id'] = $meal['id'];
                    $mealExtraIngredient = $this->addMealExtraIngredientFixture($mealExtraIngredient);
                }
            }

            return $meal;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addMealOptionFixture(array $params = [])
    {
        try {
            $defaults = [
                'name'          => 'Test Meal Option',
                'price'         => 1.0,
                'created_at'    => time(),
            ];

            $mealOptionParams       = $params + $defaults;
            $mealOptionParams['id'] = $this->persistEntity('nmtn_meal_option', $mealOptionParams);

            return $this->mapEntityDataToFormData($mealOptionParams);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addMealExtraIngredientFixture(array $params = [])
    {
        try {
            $defaults = [
                'name'          => 'Test Meal Extra Ingredient',
                'price'         => 1.0,
                'created_at'    => time(),
            ];

            $extraIngredientParams       = $params + $defaults;
            $extraIngredientParams['id'] = $this->persistEntity('nmtn_meal_extra_ingredient', $extraIngredientParams);

            return $this->mapEntityDataToFormData($extraIngredientParams);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addRestaurantOperationTimeFixture(array $params = [])
    {
        static $defaultDayOfTheWeek = [];

        try {
            if (! array_key_exists('restaurant_id', $params)) {
                $this->fail('restaurant_id is required for RestaurantOperationTime fixture');
            }

            $defaults = [
                'day_of_the_week' => 1,
                'time_from'       => 0,
                'time_to'         => 86399
            ];

            if (! array_key_exists('day_of_the_week', $params)) {
                if (! array_key_exists($params['restaurant_id'], $defaultDayOfTheWeek)) {
                    $defaultDayOfTheWeek[$params['restaurant_id']] = 1;
                }
                $defaults['day_of_the_week'] = $defaultDayOfTheWeek[$params['restaurant_id']]++;
            }

            $restaurantOperationTimeParams       = $params + $defaults;
            $restaurantOperationTimeParams['id'] = $this->persistEntity(
                'nmtn_restaurant_operation_time',
                $restaurantOperationTimeParams
            );

            return $this->mapEntityDataToFormData($restaurantOperationTimeParams);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addOrderFixture(array $params = [])
    {
        try {
            $defaults = [
                'table_number'     => 1,
                'order_status_id'  => ORDER_STATUS_NEW_ORDER,
                'product_total'    => 10,
                'discount_percent' => 5,
                'discount'         => 0.50,
                'tax_percent'      => 25,
                'sales_tax'        => 2.38,
                'tips'             => 1,
                'order_total'      => 12.88,
                'created_at'       => time()
            ];

            $orderParams       = $params + $defaults;
            $orderParams['id'] = $this->persistEntity('nmtn_order', $orderParams);

            $order = $this->mapEntityDataToFormData($orderParams);

            if (isset($params['orderMeals']) && is_array($params['orderMeals'])) {
                $order['orderMeals'] = $orderParams['orderMeals'];
                foreach ($order['orderMeals'] as &$orderMeal) {
                    $orderMeal['order_id'] = $order['id'];
                    $orderMeal             = $this->addOrderMealFixture($orderMeal);
                }
            }

            return $order;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addOrderMealFixture(array $params = [])
    {
        try {
            $defaults = [
                'quantity' => 1,
                'name' => 'test name',
                'description' => 'test description',
                'price' => 2.2,
                'discount_percent' => 2,
                'meal_option_name' => 'test option name',
                'meal_option_price' => 2.1,
                'meal_comment' => 'Order meal test comment.'
            ];

            $orderMealParams = $params + $defaults;
            $orderMealParams['id'] = $this->persistEntity('nmtn_order_meal', $orderMealParams);

            $orderMeal = $this->mapEntityDataToFormData($orderMealParams);

            if (isset($params['orderMealExtraIngredients']) && is_array($params['orderMealExtraIngredients'])) {
                $orderMeal['orderMealExtraIngredients'] = $orderMealParams['orderMealExtraIngredients'];
                foreach ($orderMeal['orderMealExtraIngredients'] as &$orderMealExtra) {
                    $orderMealExtra['order_meal_id'] = $orderMeal['id'];
                    $orderMealExtra = $this->addOrderMealExtraIngredientFixture($orderMealExtra);
                }
            }

            return $orderMeal;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addOrderMealExtraIngredientFixture(array $params)
    {
        try {
            $defaults = [
                'name' => 'test extra',
                'price' => 0.5
            ];

            $extraParams = $params + $defaults;

            $extraParams['id'] = $this->persistEntity('nmtn_order_meal_extra_ingredient', $extraParams);

            return $this->mapEntityDataToFormData($extraParams);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addConfigFixture(array $params = [])
    {
        try {
            $defaults = [
                'name'        => 'test_config_param',
                'value'       => 'test',
                'description' => 'test description',
                'system'      => 0,
                'type'        => 'text',
                'created_at'  => time()
            ];

            $configParams       = $params + $defaults;
            $configParams['id'] = $this->persistEntity('nmtn_config', $configParams);

            return $this->mapEntityDataToFormData($configParams);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addRestaurantCheckinFixture(array $params)
    {
        try {
            $time          = time();
            $defaults      = [
                'service_type_id' => 1,
                'table_number'    => 1,
                'checked_out'     => false,
                'created_at'      => $time,
                'updated_at'      => $time
            ];
            $checkinParams = $params + $defaults;
            $params['id']  = $this->persistEntity('nmtn_restaurant_checkin', $checkinParams);

            return $this->mapEntityDataToFormData($params);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addPaymentFixture(array $params = [])
    {
        try {
            $defaults = [
                'status' => 'ACCEPTED',
                'currency' => 'DKK',
                'amount' => 2500,
                'all_parameters' => 'test',
                'created_at' => time(),
                'updated_at' => time(),
            ];

            $paymentParams       = $params + $defaults;
            $paymentParams['id'] = $this->persistEntity('nmtn_payment', $paymentParams);

            return $this->mapEntityDataToFormData($paymentParams);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function addAssetFixture(array $params)
    {
        try {
            $microtime = microtime(true);
            $md5       = md5($microtime);
            $defaults  = [
                'mime_type'         => 'image/jpeg',
                'name'              => 'jpeg image asset',
                'original_filename' => 'Original name for jpeg image asset',
                'filename'          => 'Filename for jpeg image asset',
                'path'              => join('/', str_split($md5, 3)),
                'is_absolute_path'  => 1,
                'size'              => 10000,
                'width'             => 480,
                'height'            => 800,
                'md5'               => $md5,
                'created_at'        => (int) $microtime,
                'updated_at'        => null
            ];
            $assetParams = $params + $defaults;
            $params['id']  = $this->persistEntity('nmtn_asset', $assetParams);

            return $this->mapEntityDataToFormData($params);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function seeResponseContainsNumberOfEntries($count)
    {
        $this->assertResponseContainsNumberOfValuesForEntry('entries', $count);
    }

    public function seeResponseContainsNumberOfElements($json, $count)
    {
        $this->assertResponseContainsNumberOfValuesForEntry($json, $count);
    }

    private function assertEntryHasFields(array $entry, array $fields, $parentPrefix)
    {
        $childrenFields = [];
        $list           = [];
        $parentPrefix   = $parentPrefix ? $parentPrefix . '.' : '';

        foreach ($fields as $field) {
            $tokens = explode('.', $field, 3);
            \PHPUnit_Framework_Assert::assertArrayHasKey(
                $tokens[0],
                $entry,
                sprintf('field "%s" not found', $parentPrefix . $tokens[0])
            );
            if (count($tokens) > 1 && $entry[$tokens[0]] !== null) {
                \PHPUnit_Framework_Assert::assertInternalType(
                    'array',
                    $entry[$tokens[0]],
                    sprintf('field "%s" is not array', $parentPrefix . $field)
                );
                if ($tokens[1] === '[]') {
                    \PHPUnit_Framework_Assert::assertArrayHasKey(2, $tokens, '[] should contain property after self');
                    isset($list[$tokens[0]]) || $list[$tokens[0]] = [];
                    $list[$tokens[0]][] = $tokens[2];
                } else {
                    isset($childrenFields[$tokens[0]]) || $childrenFields[$tokens[0]] = [];
                    $childrenFields[$tokens[0]][] = $tokens[1] . (isset($tokens[2]) ? '.' . $tokens[2] : '');
                }
            }
        }

        foreach ($childrenFields as $field => $childFields) {
            $this->assertEntryHasFields($entry[$field], $childFields, $parentPrefix . $field);
        }

        $parentPrefix = $parentPrefix ? $parentPrefix . '[].' : '';
        foreach ($list as $field => $childFields) {
            foreach ($entry[$field] as $item) {
                $this->assertEntryHasFields($item, $childFields, $parentPrefix . $field);
            }
        }
    }

    public function seeResponseEntriesHasFields(array $fields)
    {
        \PHPUnit_Framework_Assert::assertNotEmpty($fields, __FUNCTION__ . ': $fields can not be empty');

        /** @var $rest REST */
        $rest = $this->getModule('REST');
        $data = $rest->grabDataFromJsonResponse('entries');
        \PHPUnit_Framework_Assert::assertGreaterThan(0, count($data), "JSON response does not contain any entry");

        foreach ($data as $entry) {
            $this->assertEntryHasFields($entry, $fields, '');
        }
    }

    public function seeInDatabaseNumberOfRows($table, $count, $criteria = array())
    {
        /** @var $db DB */
        $db = $this->getModule('Db');
        $res = $db->grabFromDatabase($table, 'count(1)', $criteria);
        \PHPUnit_Framework_Assert::assertEquals($count, $res);
    }

    public function markTestIncomplete($message)
    {
        throw new \PHPUnit_Framework_IncompleteTestError($message);
    }

    public function clearDbTable($tableName)
    {
        /** @var $db \PDO */
        $db = $this->getModule('Db')->driver->getDbh();

        $query = $db->prepare("DELETE FROM $tableName");
        $query->execute();
    }
}
