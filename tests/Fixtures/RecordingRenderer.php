<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Fixtures;

use CommonPHP\UI\Contracts\AbstractRenderer;
use CommonPHP\UI\Contracts\ComponentInterface;
use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\UI\View;
use CommonPHP\UI\ViewData;

final class RecordingRenderer extends AbstractRenderer
{
    /**
     * @var list<string>
     */
    public array $calls = [];

    public function render(View $view): string
    {
        $this->calls[] = 'render:' . $view->template()->name();

        return 'rendered:' . $view->template()->name() . ':' . $view->data()->get('id', 'none');
    }

    public function renderTemplate(TemplateInterface|string $template, array|ViewData $data = []): string
    {
        $template = $this->template($template);
        $this->calls[] = 'template:' . $template->name();

        return 'template:' . $template->name();
    }

    public function renderComponent(ComponentInterface|string $component, array|ViewData $data = []): string
    {
        $component = $this->component($component);
        $this->calls[] = 'component:' . $component->componentName();

        return 'component:' . $component->componentName();
    }
}
