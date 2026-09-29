# Markdown Rendering

MonKeysLegion provides a pure PHP Markdown renderer that supports the most common CommonMark syntax — no external dependencies required.

## Usage

```php
use MonkeysLegion\Markdown\MarkdownRenderer;

$renderer = $container->get(MarkdownRenderer::class);
$html = $renderer->render('# Hello World');
// Output: <h1>Hello World</h1>
```

## Supported Syntax

| Element | Syntax | Output |
|---------|--------|--------|
| H1-H6 | `# Header` | `<h1>Header</h1>` |
| Setext H1 | `Title\n===` | `<h1>Title</h1>` |
| Setext H2 | `Subtitle\n---` | `<h2>Subtitle</h2>` |
| Bold | `**text**` or `__text__` | `<strong>text</strong>` |
| Italic | `*text*` or `_text_` | `<em>text</em>` |
| Strikethrough | `~~text~~` | `<del>text</del>` |
| Inline code | `` `code` `` | `<code>code</code>` |
| Code block | ` ```lang\ncode\n``` ` | `<pre><code class="language-lang">code</code></pre>` |
| Indented code | `    code` | `<pre><code>code</code></pre>` |
| Link | `[text](url)` | `<a href="url">text</a>` |
| Image | `![alt](url)` | `<img src="url" alt="alt">` |
| Unordered list | `- item` | `<ul><li>item</li></ul>` |
| Ordered list | `1. item` | `<ol><li>item</li></ol>` |
| Blockquote | `> quote` | `<blockquote>quote</blockquote>` |
| Horizontal rule | `---` | `<hr>` |
| Paragraph | `text` | `<p>text</p>` |

## Security

Code blocks and inline code are HTML-escaped to prevent XSS attacks:

```php
$renderer->render("```\n<script>alert('xss')</script>\n```");
// Output: <pre><code>&lt;script&gt;alert('xss')&lt;/script&gt;</code></pre>
```
