<?php

namespace App\View\Components;

use App\Models\Branch;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Component;

class ChatbotWidget extends Component
{
    /**
     * @var Collection<int, Branch>
     */
    public Collection $branches;

    public function __construct()
    {
        $this->branches = Schema::hasTable('branches')
            ? Branch::query()->orderBy('name')->get(['id', 'name'])
            : collect();
    }

    public function render(): View
    {
        return view('components.chatbot-widget');
    }
}
