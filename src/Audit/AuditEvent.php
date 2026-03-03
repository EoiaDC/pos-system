<?php
namespace App\Audit;
class AuditEvent
{
    public ?int $actor_user_id = null;
    public string $action;
    public string $entity_type;
    public ?string $entity_id = null;
    public array $meta = [];
    public string $ip_address;
    public string $user_agent;
    public string $created_at;

    public function __construct(string $action, string $entity_type)
    {
        $this->action = $action;
        $this->entity_type = $entity_type;
        $this->ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $this->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $this->created_at = date('Y-m-d H:i:s');
    }
}
