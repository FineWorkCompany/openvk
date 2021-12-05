<?php declare(strict_types=1);
namespace openvk\Web\Models\Repositories;
use openvk\Web\Models\Entities\Report;
use Nette\Database\Table\ActiveRow;
use Chandler\Database\DatabaseConnection;

class Reports
{
    private $context;
    private $reports;
    
    function __construct()
    {
        $this->context = DatabaseConnection::i()->getContext();
        $this->reports = $this->context->table("reports");
    }
    
    private function toReport(?ActiveRow $ar): ?Report
    {
        return is_null($ar) ? NULL : new Report($ar);
    }
    
    function getReports(int $state = 0, int $page = 1): \Traversable
    {
        foreach($this->reports->where(["deleted" => 0])->order("created DESC")->page($page, 15) as $t)
            yield new Report($t);
    }
    
    function getReportsCount(int $state = 0): int
    {
        return sizeof($this->reports->where(["deleted" => 0, "type" => $state]));
    }
    
    function get(int $id): ?Report
    {
        return $this->toReport($this->reports->get($id));
    }
    
    function getByContentId(int $id): ?Report
    {
        $post = $this->reports->where(["deleted" => 0, "content_id" => $id])->fetch();

        if($post)
            return new Report($post);
        else
            return null; 
    }
   
    use \Nette\SmartObject;
}
