<?php
/**
 *
 * @author     Shailesh Parsana <shaileshparsana@gmail.com>
 *
 */
namespace Srp\ExtendsImport\Model\Import\Product;

use Magento\Framework\Exception\AlreadyExistsException;

class CategoryProcessor extends \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor
{
    /**
     * Returns IDs of categories by string path creating nonexistent ones.
     *
     * @param string $categoriesString
     * @param string $categoriesSeparator
     * @return array
     */
    public function upsertCategories($categoriesString, $categoriesSeparator)
    {
        $categoriesIds = [];
        $categories    = explode($categoriesSeparator, $categoriesString);

        foreach ($categories as $category) {
            try {
                if (is_numeric($category) && $this->getCategoryById($category)) {
                    $categoriesIds[] = $category;
                }
                else {
                    $categoriesIds[] = $this->upsertCategory($category);
                }
            }
            catch (AlreadyExistsException $e) {
                $this->addFailedCategory($category, $e);
            }
        }

        return $categoriesIds;
    }

    /**
     * Add failed category
     *
     * @param string $category
     * @param \Magento\Framework\Exception\AlreadyExistsException $exception
     *
     * @return $this
     */
    private function addFailedCategory($category, $exception)
    {
        $this->failedCategories[] =
            [
                'category'  => $category,
                'exception' => $exception,
            ];

        return $this;
    }
}
