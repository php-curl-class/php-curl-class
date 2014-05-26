activate :syntax
activate :relative_assets
set :markdown_engine, :redcarpet
set :markdown,
  :disable_indented_code_blocks => true,
  :fenced_code_blocks => true,
  :no_intra_emphasis => true,
  :prettify => true,
  :smartypants => true,
  :tables => true,
  :with_toc_data => true

set :css_dir, 'css'
set :js_dir, 'js'

configure :build do
  activate :minify_css
  activate :minify_javascript
end
