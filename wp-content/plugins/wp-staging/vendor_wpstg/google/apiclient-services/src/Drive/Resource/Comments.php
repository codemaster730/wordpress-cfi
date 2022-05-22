<?php

/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
namespace WPStaging\Vendor\Google\Service\Drive\Resource;

use WPStaging\Vendor\Google\Service\Drive\Comment;
use WPStaging\Vendor\Google\Service\Drive\CommentList;
/**
 * The "comments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $driveService = new Google\Service\Drive(...);
 *   $comments = $driveService->comments;
 *  </code>
 */
class Comments extends \WPStaging\Vendor\Google\Service\Resource
{
    /**
     * Creates a new comment on a file. (comments.create)
     *
     * @param string $fileId The ID of the file.
     * @param Comment $postBody
     * @param array $optParams Optional parameters.
     * @return Comment
     */
    public function create($fileId, \WPStaging\Vendor\Google\Service\Drive\Comment $postBody, $optParams = [])
    {
        $params = ['fileId' => $fileId, 'postBody' => $postBody];
        $params = \array_merge($params, $optParams);
        return $this->call('create', [$params], \WPStaging\Vendor\Google\Service\Drive\Comment::class);
    }
    /**
     * Deletes a comment. (comments.delete)
     *
     * @param string $fileId The ID of the file.
     * @param string $commentId The ID of the comment.
     * @param array $optParams Optional parameters.
     */
    public function delete($fileId, $commentId, $optParams = [])
    {
        $params = ['fileId' => $fileId, 'commentId' => $commentId];
        $params = \array_merge($params, $optParams);
        return $this->call('delete', [$params]);
    }
    /**
     * Gets a comment by ID. (comments.get)
     *
     * @param string $fileId The ID of the file.
     * @param string $commentId The ID of the comment.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool includeDeleted Whether to return deleted comments. Deleted
     * comments will not include their original content.
     * @return Comment
     */
    public function get($fileId, $commentId, $optParams = [])
    {
        $params = ['fileId' => $fileId, 'commentId' => $commentId];
        $params = \array_merge($params, $optParams);
        return $this->call('get', [$params], \WPStaging\Vendor\Google\Service\Drive\Comment::class);
    }
    /**
     * Lists a file's comments. (comments.listComments)
     *
     * @param string $fileId The ID of the file.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool includeDeleted Whether to include deleted comments. Deleted
     * comments will not include their original content.
     * @opt_param int pageSize The maximum number of comments to return per page.
     * @opt_param string pageToken The token for continuing a previous list request
     * on the next page. This should be set to the value of 'nextPageToken' from the
     * previous response.
     * @opt_param string startModifiedTime The minimum value of 'modifiedTime' for
     * the result comments (RFC 3339 date-time).
     * @return CommentList
     */
    public function listComments($fileId, $optParams = [])
    {
        $params = ['fileId' => $fileId];
        $params = \array_merge($params, $optParams);
        return $this->call('list', [$params], \WPStaging\Vendor\Google\Service\Drive\CommentList::class);
    }
    /**
     * Updates a comment with patch semantics. (comments.update)
     *
     * @param string $fileId The ID of the file.
     * @param string $commentId The ID of the comment.
     * @param Comment $postBody
     * @param array $optParams Optional parameters.
     * @return Comment
     */
    public function update($fileId, $commentId, \WPStaging\Vendor\Google\Service\Drive\Comment $postBody, $optParams = [])
    {
        $params = ['fileId' => $fileId, 'commentId' => $commentId, 'postBody' => $postBody];
        $params = \array_merge($params, $optParams);
        return $this->call('update', [$params], \WPStaging\Vendor\Google\Service\Drive\Comment::class);
    }
}
// Adding a class alias for backwards compatibility with the previous class name.
\class_alias(\WPStaging\Vendor\Google\Service\Drive\Resource\Comments::class, 'WPStaging\\Vendor\\Google_Service_Drive_Resource_Comments');