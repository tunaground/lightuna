<?php

namespace Lightuna\Util;

class IdGenerator
{
    public function gen(string $source)
    {
        return $this->adj[$source % sizeof($this->adj)]
            . $this->fish[$source % sizeof($this->fish)]
            . $this->recipe[$source % sizeof($this->recipe)];
    }

    private $adj = [
        '⭐', '🐳', '🐋', '🐬', '🐟', '🐠', '🐡', '🦈', '🐙', '🐚',
        '🪸', '🪼', '🦀', '🦞', '🦐', '🦑', '🦪', '🍣', '🍤',
        '가냘픈', '가녀린', '가벼운', '가여운', '간지러운', '고마운', '곧은', '괴로운', '구린', '굳은',
        '굵은', '굽은', '기쁜', '깊은', '까만', '껄끄러운', '날랜', '날쌘', '날카로운', '나은',
        '낮은', '너그러운', '넓은', '노란', '놀라운', '높은', '누런', '느린', '늦은', '더운',
        '동그란', '두려운', '따가운', '떫은', '뜨거운', '마려운', '많은', '말간', '맑은', '매끄러운',
        '매서운', '매스꺼운', '무거운', '무딘', '무서운', '묽은', '미끄러운', '미운', '바쁜', '반가운',
        '밝은', '버거운', '벅찬', '보드라운', '부끄러운', '부러운', '붉은', '비린', '비싼', '빨간',
        '뽀얀', '뿌연', '사나운', '살가운', '서글픈', '서러운', '서툰', '센', '수줍은', '슬픈',
        '시끄러운', '신', '싱거운', '싼', '쓴', '쓰라린', '아니꼬운', '아린', '아쉬운', '아픈',
        '안쓰러운', '안타까운', '애달픈', '약은', '얕은', '어두운', '어린', '어리석은', '어설픈', '어여쁜',
        '어지러운', '없는', '여린', '옅은', '예쁜', '옳은', '외로운', '이쁜', '익은', '자그만',
        '작은', '재밌는', '저린', '적은', '젊은', '점잖은', '조그만', '즐거운', '지겨운', '지나친',
        '질긴', '징그러운', '짙은', '짠', '짧은', '차가운', '추운', '파란', '하얀', '하찮은', '후진',
    ];
    private $fish = [
        '⭐', '🐳', '🐋', '🐬', '🐟', '🐠', '🐡', '🦈', '🐙', '🐚',
        '🪸', '🪼', '🦀', '🦞', '🦐', '🦑', '🦪', '🍣', '🍤',
        '가다랑어', '가오리', '가자미', '갈치', '감성돔', '개복치', '고등어', '광어', '괴도라치', '까나리',
        '꼼장어', '꽁치', '날치', '노래미', '농어', '능성어', '달고기', '대구', '도다리', '도루묵',
        '도미', '독가시치', '돌돔', '돔배기', '돗돔', '만새기', '망상어', '메기', '멸치', '명태',
        '문절망둑', '물메기', '미꾸라지', '민어', '방어', '배스', '밴댕이', '뱅어', '벵에돔', '병어',
        '보리멸', '복어', '볼락', '부세', '부시리', '붕어', '붕장어', '블루길', '비막치어', '빙어',
        '산천어', '삼치', '서대', '송사리', '송어', '숭어', '시샤모', '쏘가리', '쏠배감펭', '쏨뱅이',
        '아귀', '연어', '열빙어', '우럭', '은어', '임연수어', '잉어', '자갈치', '장어', '전갱이',
        '전어', '점성어', '정어리', '조기', '준치', '쥐치', '참돔', '참치', '청새치', '청어',
        '향어', '홍어', '황새치',
    ];
    private $recipe = [
        '⭐', '🐳', '🐋', '🐬', '🐟', '🐠', '🐡', '🦈', '🐙', '🐚',
        '🪸', '🪼', '🦀', '🦞', '🦐', '🦑', '🦪', '🍣', '🍤',
        '맛살', '찜', '구이', '조림', '튀김', '회', '덮밥', '국', '까스', '초밥',
        '탕', '버거', '피자', '전', '볶음', '찌개', '김치', '장', '쌈', '젓갈', '무침',
        '포', '빵',
    ];
}