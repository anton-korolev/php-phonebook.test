<?php

declare(strict_types=1);

namespace common\helpers;

class Pagination
{
    static function render(
        int $pageCount,
        int $currentPage,
        int $frameLength,
    ): string {
        $result = <<<"END"
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
        END;

        $first = intdiv($currentPage - 1, $frameLength) * $frameLength + 1;
        if (($first + $frameLength - 1 > $pageCount)
            && ($pageCount > $frameLength)
        ) {
            $first = $pageCount - $frameLength + 1;
        }
        // Render Previous button
        if (1 === $first) {
            $result .= <<<"END"
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">&laquo;</a>
                    </li>
            END;
        } else {
            $pageNum = $first - 1;
            $result .= <<<"END"
                    <li class="page-item">
                        <a class="page-link" href="?page=$pageNum">&laquo;</a>
                    </li>
            END;
        }
        // Render Page buttons
        for ($i = $first; ($i < $first + $frameLength) && ($i <= $pageCount); $i++) {
            if ($i === $currentPage) {
                $result .= <<<"END"
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">$i</span>
                        </li>
                END;
            } else {
                $result .= <<<"END"
                        <li class="page-item"><a class="page-link" href="?page=$i">$i</a></li>
                END;
            }
        }
        // Render Next button
        if ($first + $frameLength - 1 < $pageCount) {
            $pageNum = $first + $frameLength;
            $result .= <<<"END"
                    <li class="page-item">
                        <a class="page-link" href="?page=$pageNum">&raquo;</a>
                    </li>
            END;
        } else {
            $result .= <<<"END"
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">&raquo;</a>
                    </li>
            END;
        }

        $result .= <<<"END"
                </ul>
            </nav>
        END;

        return $result;
    }


    // <nav aria-label="Page navigation">
    //     <ul class="pagination justify-content-center">
    //         <li class="page-item disabled">
    //             <a class="page-link" href="#" tabindex="-1" aria-disabled="true">&laquo;</a>
    //         </li>

    //         <li class="page-item"><a class="page-link" href="#">1</a></li>
    //         <li class="page-item active" aria-current="page">
    //             <span class="page-link">2</span>
    //         </li>
    //         <li class="page-item"><a class="page-link" href="#">3</a></li>

    //         <li class="page-item">
    //             <a class="page-link" href="#">&raquo;</a>
    //         </li>
    //     </ul>



}
