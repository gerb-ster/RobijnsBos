<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;
use Throwable;
use ReflectionException;
use ReflectionMethod;

class ApiDocumentationController extends Controller
{
  /**
   * Return an OpenAPI document for the registered API controller routes.
   *
   * @param Router $router
   * @return JsonResponse
   * @throws ReflectionException
   */
  public function __invoke(Router $router): JsonResponse
  {
    $paths = [];

    foreach ($router->getRoutes() as $route) {
      $action = $route->getActionName();

      if (!str_starts_with($action, 'App\\Http\\Controllers\\Api\\')) {
        continue;
      }

      [$controller, $method] = explode('@', $action, 2);
      $path = '/' . $route->uri();

      foreach (array_diff($route->methods(), ['HEAD']) as $httpMethod) {
        $paths[$path][strtolower($httpMethod)] = [
          'operationId' => $route->getName(),
          'summary' => $this->getSummary($controller, $method),
          'parameters' => $this->getQueryParameters($controller, $method),
          'responses' => [
            '200' => ['description' => 'Successful response'],
          ],
        ];
      }
    }

    ksort($paths);

    return response()->json([
      'openapi' => '3.0.3',
      'info' => [
        'title' => config('app.name') . ' API',
        'version' => '1.0.0',
      ],
      'paths' => $paths,
    ]);
  }

  /**
   * @param string $controller
   * @param string $method
   * @return string|null
   * @throws ReflectionException
   */
  private function getSummary(string $controller, string $method): ?string
  {
    $docComment = (new ReflectionMethod($controller, $method))->getDocComment();

    if ($docComment === false) {
      return null;
    }

    foreach (preg_split('/\R/', $docComment) as $line) {
      $line = trim($line, " \t*\/");

      if ($line !== '' && !str_starts_with($line, '@')) {
        return $line;
      }
    }

    return null;
  }

  /**
   * @param string $controller
   * @param string $method
   * @return array<int, array<string, mixed>>
   * @throws ReflectionException
   */
  private function getQueryParameters(string $controller, string $method): array
  {
    $parameters = [];
    $reflectionMethod = new ReflectionMethod($controller, $method);

    foreach ($reflectionMethod->getParameters() as $parameter) {
      $requestClass = $parameter->getType()?->getName();

      if ($requestClass === null || !is_subclass_of($requestClass, FormRequest::class)) {
        continue;
      }

      $rules = (new ReflectionMethod($requestClass, 'rules'))->invoke(new $requestClass());

      if (!is_array($rules)) {
        continue;
      }

      foreach ($rules as $name => $rule) {
        if (str_contains($name, '.')) {
          continue;
        }

        $rulesForField = is_array($rule) ? $rule : explode('|', $rule);

        if (!in_array('array', $rulesForField, true)) {
          continue;
        }

        $itemRules = $rules[$name . '.*'] ?? [];
        $itemRules = is_array($itemRules) ? $itemRules : explode('|', $itemRules);
        $itemSchema = ['type' => 'string'];
        $existsRule = null;

        foreach ($itemRules as $itemRule) {
          if (is_string($itemRule) && str_starts_with($itemRule, 'exists:')) {
            $existsRule = $itemRule;
            break;
          }
        }

        if ($existsRule !== null) {
          [$table, $column] = explode(',', substr($existsRule, strlen('exists:')));
          $itemSchema['description'] = 'Must match a value in ' . $table . '.' . $column . '.';
          $values = $this->getExistingValues($table, $column);

          if ($values !== []) {
            $itemSchema['enum'] = $values;
          }
        }

        $parameters[] = [
          'name' => $name . '[]',
          'in' => 'query',
          'required' => in_array('required', $rulesForField, true),
          'schema' => [
            'type' => 'array',
            'items' => $itemSchema,
          ],
        ];
      }
    }

    return $parameters;
  }

  /**
   * @param string $table
   * @param string $column
   * @return array<int, string>
   */
  private function getExistingValues(string $table, string $column): array
  {
    try {
      return DB::table($table)->orderBy($column)->pluck($column)->filter()->values()->all();
    } catch (Throwable) {
      return [];
    }
  }
}
