<?php
require_once __DIR__ . '/../models/Todo.php';

class TodoController {
    private $model;

    public function __construct($pdo) {
        $this->model = new Todo($pdo);
    }

    private function jsonResponse($data, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    // GET /todos
    public function index() {
        $todos = $this->model->getAll();
        $this->jsonResponse($todos);
    }

    // GET /todos/{id}
    public function show($id) {
        $todo = $this->model->getById($id);
        if ($todo) {
            $this->jsonResponse($todo);
        } else {
            $this->jsonResponse(['error' => 'Todo not found'], 404);
        }
    }

    // POST /todos
    public function store() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || empty($input['title'])) {
            $this->jsonResponse(['error' => 'Invalid input'], 400);
        }

        try {
            $id = $this->model->create($input);
            $this->jsonResponse([
                'message' => 'Todo created successfully',
                'id' => $id
            ], 201);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Failed to create todo', 'details' => $e->getMessage()], 500);
        }
    }

    // PUT /todos/{id}
    public function update($id) {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $this->jsonResponse(['error' => 'Invalid input'], 400);
        }

        try {
            $success = $this->model->update($id, $input);
            if ($success) {
                $this->jsonResponse(['message' => 'Todo updated successfully']);
            } else {
                $this->jsonResponse(['error' => 'Todo not found or no change made'], 404);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Update failed', 'details' => $e->getMessage()], 500);
        }
    }

    // DELETE /todos/{id}
    public function delete($id) {
        try {
            $success = $this->model->delete($id);
            if ($success) {
                $this->jsonResponse(['message' => 'Todo deleted successfully']);
            } else {
                $this->jsonResponse(['error' => 'Todo not found'], 404);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Delete failed', 'details' => $e->getMessage()], 500);
        }
    }
}
