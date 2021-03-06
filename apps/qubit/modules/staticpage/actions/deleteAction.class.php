<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

class StaticPageDeleteAction extends sfAction
{
  public function execute($request)
  {
    $this->form = new sfForm;

    $this->resource = $this->getRoute()->resource;

    // Check user authorization
    if ($this->resource->isProtected())
    {
      QubitAcl::forwardUnauthorized();
    }

    if ($request->isMethod('delete'))
    {
      $this->resource->delete();

      // Invalidate static page content cache entry
      if (null !== $cache = QubitCache::getInstance())
      {
        $languages = QubitSetting::getByScope('i18n_languages');
        foreach ($languages as $culture)
        {
          $cacheKey = 'staticpage:'.$this->resource->id.':'.$culture;
          $cache->remove($cacheKey);
        }
      }

      $this->redirect(array('module' => 'staticpage', 'action' => 'list'));
    }
  }
}
