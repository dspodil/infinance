select
  aa.id,aa.subjectid,aaa.inn,aa.ckiid,aaa.dateApplication,
  (
    select
      count(distinct a.id) cnt
    from
      contractsMBKI a
    where
      (
        (`CreditorType` = 'Фінансова компанія')
        or (
          `CreditorType` = 'Фінансова компанія - онлайн кредитування'
        )
        or (PurposeOfCredit = 'Кредит на карту')
      )
      and `TotalAmountValue` <= 20000
      and a.subjectid = aa.subjectid
      and not(
        `ContractPhase` = 'Закінчено'
        or `ContractPhase` = 'Припинено достроково'
      )
      and (ContractEndDate between aaa.dateApplication - INTERVAL 366 Day
      AND sysdate() or ContractEndDate='1970-01-01 00:00:00')
  ) '1.	Кількість відкритих кредитів МФО в МБКІ ',
  (
    SELECT
      count(distinct a.id)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and (
        a.dldonor = 'FIN'
        or a.dldonor = 'MFO'
      )
      and b.dlds between b.dldpf - INTERVAL 367 day
      and b.dldpf
      
      and `dlflstat` in( 1,5)
	    and b.id =(select max(id) from deallifeUBKI where crdealid=b.crdealid )
		and exists(select 1 from historyLog where applicationid=aa.id and algId=1 and Text like '%УБКІ%')
  ) '2.	Кількість відкритих кредитів МФО в УБКІ ',
  (
    SELECT
      count(contracts.id)
    FROM
      `contractsMBKI` contracts
    WHERE
      (
        (`CreditorType` = 'Фінансова компанія')
        or (
          `CreditorType` = 'Фінансова компанія - онлайн кредитування'
        )
        or (PurposeOfCredit = 'Кредит на карту')
      )
      and `TotalAmountValue` <= 20000
      and contracts.SubjectId = aa.subjectid
     
      and `TotalAmountValue` <= 20000
  ) '3.	Кількість кредитів МФО в МБКІ, (це параметр який є в скорингу - "Наявність відкритих МФО/Кіл МФО в МБКІ")',
  (
    select
      count(distinct a.id)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    where
      b.cki_id = aa.ckiid
      and `dlflstat` = 2
  ) '4.	Кількість закритих кредитів в УБКІ ',
  (
    SELECT
      count(distinct a.id)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and (
        a.dldonor = 'FIN'
        or a.dldonor = 'MFO'
      )
      and b.dlds between b.dldpf - INTERVAL 367 day
      and b.dldpf
      and a.dlamt <= 20000
      and a.cki_id = aa.ckiid
	    and b.id =(select max(id) from deallifeUBKI where crdealid=b.crdealid )
  ) '5.	Кількість кредитів МФО в УБКІ ',
  (
    select
      count(distinct a.id) cnt
    from
      contractsMBKI a
    where
      (
        (`CreditorType` = 'Фінансова компанія')
        or (
          `CreditorType` = 'Фінансова компанія - онлайн кредитування'
        )
        or (PurposeOfCredit = 'Кредит на карту')
      )
      and `TotalAmountValue` <= 20000
      and a.subjectid = aa.subjectid
      and (
        `ContractPhase` = 'Закінчено'
        or `ContractPhase` = 'Припинено достроково'
      )
      and LastUpdateContract between aaa.dateApplication- INTERVAL 366 Day
      AND aaa.dateApplication
  ) '6.	Кількість закритих кредитів МФО в МБКІ ',
  (
    SELECT
      count(distinct a.id)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and (
        a.dldonor = 'FIN'
        or a.dldonor = 'MFO'
      )
      and b.dlds between b.dldpf - INTERVAL 366 day
      and b.dldpf
      and a.dlamt <= 20000
      and `dlflstat` = 2
      and b.dldateclc between aaa.dateApplication - INTERVAL 366 Day
      AND aaa.dateApplication
  ) '7.	Кількість закритих кредитів МФО в УБКІ ',
  (
    select
      max(
        ifnull(month1value, 0) + ifnull(month2value, 0) + ifnull(month3value, 0) + ifnull(month4value, 0) + ifnull(month5value, 0) + ifnull(month6value, 0) + ifnull(month7value, 0) + ifnull(month8value, 0) + ifnull(month9value, 0) + ifnull(month10value, 0) + ifnull(month11value, 0) + ifnull(month12value, 0)
      ) as cnt
    from
      contractsMBKI a
      join historicalcalendarsMBKI b on a.id = b.contractid
    where
       (
        (`CreditorType` = 'Фінансова компанія')
        or (
          `CreditorType` = 'Фінансова компанія - онлайн кредитування'
        )
        or (PurposeOfCredit = 'Кредит на карту')
      )
      and `TotalAmountValue` <= 20000
      and b.subjectid = aa.subjectid
      and (
        b.description = 'Сумарна кількіс'
        or b.description = 'Сумарна кількість просторочених платежів'
      )
  ) '8.	Сумарна кількість прострочених виплат по кредитах МФО в МБКІ',
  (
    SELECT
      count(b.id)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and (
        a.dldonor = 'FIN'
        or a.dldonor = 'MFO'
      )
      and b.dlds between b.dldpf - INTERVAL 367 day
      and b.dldpf
      and a.dlamt <= 20000
      and dlamtexp > 0
  ) '9.	Сумарна кількість прострочених виплат по кредитах МФО в УБКІ',
  (
    SELECT
      count(suma)
    FROM
       table60 
    WHERE
      `SubjectId` = aa.subjectid
  ) '10.	Сумарна кількість прострочених виплат по кредитах МФО в МБКІ поточних ',
  (
    SELECT
       sum(TIMESTAMPDIFF(MONTH,(select min(STR_TO_DATE(concat(dlmonth ,'/01/',dlyear ) ,'%m/%d/%Y')) from deallifeUBKI where crdealid=b.crdealid and STR_TO_DATE(concat(dlmonth ,'/01/',dlyear ) ,'%m/%d/%Y') =(
        select
          max(STR_TO_DATE(concat(dlmonth ,'/01/',dlyear ) ,'%m/%d/%Y'))  from
          deallifeUBKI
        where
          crdealid = b.crdealid and dlamtexp=0)), STR_TO_DATE(concat(dlmonth ,'/01/',dlyear ) ,'%m/%d/%Y')))
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
     b.dlamtexp>0 and
      b.cki_id = aa.ckiid
      and (
        a.dldonor = 'FIN'
        or a.dldonor = 'MFO'
      )
      and b.dlds between b.dldpf - INTERVAL 367 day
      and b.dldpf
      and a.dlamt <= 20000
     and STR_TO_DATE(concat(dlmonth ,'/01/',dlyear ) ,'%m/%d/%Y') =(
        select
          max(STR_TO_DATE(concat(dlmonth ,'/01/',dlyear ) ,'%m/%d/%Y'))
        from
          deallifeUBKI
        where
          crdealid = b.crdealid
      )
     
  ) '11.	Сумарна кількість прострочених виплат по кредитах МФО в УБКІ поточних',
  (
    SELECT
      sum(DueInterestAmountValue)
    FROM
      `contractsMBKI`
    WHERE
     (
        (`CreditorType` = 'Фінансова компанія')
        or (
          `CreditorType` = 'Фінансова компанія - онлайн кредитування'
        )
        or (PurposeOfCredit = 'Кредит на карту')
      )
      and `TotalAmountValue` <= 20000
      and `SubjectId` = aa.subjectid
  ) '12.	Загальна сума прострочених виплат (поточних) по кредитах МФО в МБКІ',
  (
    SELECT
      sum(b.dlamtexp)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and (
        a.dldonor = 'FIN'
        or a.dldonor = 'MFO'
      )
      and b.dlds between b.dldpf - INTERVAL 367 day
      and b.dldpf
      and a.dlamt <= 20000
      and dldateclc =(
        select
          max(dldateclc)
        from
          deallifeUBKI
        where
          crdealid = b.crdealid
      )
      and b.id =(
        select
          max(id)
        from
          deallifeUBKI
        where
          crdealid = b.crdealid
      )
  ) '13.	Загальна сума прострочених виплат (поточних) по кредитах МФО в УБКІ ',
  (
    (
      (
        SELECT
          sum(OverdueAmountValue)
        FROM
          `contractsMBKI`
        WHERE
          not(
            (
              (`CreditorType` = 'Фінансова компанія')
              or (
                `CreditorType` = 'Фінансова компанія - онлайн кредитування'
              )
              or (PurposeOfCredit = 'Кредит на карту')
            )
            and `TotalAmountValue` <= 20000
          )
          and `SubjectId` = aa.subjectid
      )
    )
  ) '14.	Сума поточних прострочених виплат по інших кредитах, НЕ кредитах МФО  в МБКІ',
  (
    (
      SELECT
        sum(b.dlamtexp)
      from
        crdealUBKI a
        join deallifeUBKI b on a.id = b.crdealid
      WHERE
        b.cki_id = aa.ckiid
        and dldateclc =(
          select
            max(dldateclc)
          from
            deallifeUBKI
          where
            crdealid = b.crdealid
        )
        and b.id =(
          select
            max(id)
          from
            deallifeUBKI
          where
            crdealid = b.crdealid
        )
        and not(
          (
            a.dldonor = 'FIN'
            or a.dldonor = 'MFO'
          )
          and b.dlds between b.dldpf - INTERVAL 367 day
          and b.dldpf
          and a.dlamt <= 20000
        )
    )
  ) '15.	Сума поточних прострочених виплат по інших кредитах, НЕ кредитах МФО  в УБКІ ',
  (
    (
      select
        max(
          GREATEST(
            ifnull(month1value, 0),
            ifnull(month2value, 0),
            ifnull(month3value, 0),
            ifnull(month4value, 0),
            ifnull(month5value, 0),
            ifnull(month6value, 0),
            ifnull(month7value, 0),
            ifnull(month8value, 0),
            ifnull(month9value, 0),
            ifnull(month10value, 0),
            ifnull(month11value, 0),
            ifnull(month12value, 0)
          )
        ) as cnt
      from
        contractsMBKI a
        join historicalcalendarsMBKI b on a.id = b.contractid
      where
        OverdueAmountValue > 0
        and not(
          (
            (`CreditorType` = 'Фінансова компанія')
            or (
              `CreditorType` = 'Фінансова компанія - онлайн кредитування'
            )
            or (PurposeOfCredit = 'Кредит на карту')
          )
          and `TotalAmountValue` <= 20000
        )
        and b.subjectid = aa.subjectid
        and (
          b.description = 'Сумарна кількіс'
          or b.description = 'Сумарна кількість просторочених платежів'
        )
    )
  ) '16.	Максимальний к-сть прострочених платежів по кредитах НЕ МФО, МБКІ',
  (
    select max(b.dldayexp)
     from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and not(
        (
          a.dldonor = 'FIN'
          or a.dldonor = 'MFO'
        )
        and b.dlds between b.dldpf - INTERVAL 367 day
        and b.dldpf
        and a.dlamt <= 20000
      )
      and dldateclc  between aaa.dateApplication - INTERVAL 360 day and aaa.dateApplication
  ) '17.	Максимальна к-сть днів поточної прострочки по кредитах НЕ МФО, УБКІ',
  (
    select
      sum(
        greatest(
          b.month1value,
          b.month2value,
          b.month3value,
          b.month4value,
          b.month5value,
          b.month6value,
          b.month7value,
          b.month8value,
          b.month9value,
          b.month10value,
          b.month11value,
          b.month12value
        )
      )/100
    from
      contractsMBKI a
      join historicalcalendarsMBKI b on a.id = b.contractid
    where
      (
        
        
		  not(
          (`CreditorType` = 'Фінансова компанія')
          or (
            `CreditorType` = 'Фінансова компанія - онлайн кредитування'
          )
          or (PurposeOfCredit = 'Кредит на карту')
        )
        and `TotalAmountValue` <= 20000
      )
      and a.subjectid = aa.subjectid
      and cast(concat('20',SUBSTRING_INDEX(month12Name, '/', -1), case when length(SUBSTRING_INDEX(month12Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month12Name, '/', 1) ) else 
		SUBSTRING_INDEX(month12Name, '/', 1) end,'01') as date) between aaa.dateApplication - INTERVAL 360 Day AND aaa.dateApplication
  
      and b.type = '-12'
      and b.description = 'Несплачена прострочена сума платежів'
    
  ) '18.	Сума прострочки не МФО за останній рік МБКІ',
  ( select sum(b.dlamtexp)
     from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and not(
        (
          a.dldonor = 'FIN'
          or a.dldonor = 'MFO'
        )
        and b.dlds between b.dldpf - INTERVAL 367 day
        and b.dldpf
        and a.dlamt <= 20000
      )
       and STR_TO_DATE(concat(dlmonth ,'/01/',dlyear ) ,'%m/%d/%Y') between (aaa.dateApplication - INTERVAL 360 day) 
        and aaa.dateApplication
  ) '18.	Сума прострочки не МФО за останній рік УБКІ',
  (
    select
      sum(
        
         
          b.month11value
         
        
      )/100
    from
      contractsMBKI a
      join historicalcalendarsMBKI b on a.id = b.contractid
    where
      (
         (
          (`CreditorType` = 'Фінансова компанія')
          or (
            `CreditorType` = 'Фінансова компанія - онлайн кредитування'
          )
          or (PurposeOfCredit = 'Кредит на карту')
        )
        and `TotalAmountValue` <= 20000
      )
      and a.subjectid = aa.subjectid
      and ContractType <> 'Existing'
      and b.type = '-12'
      and b.description = 'Несплачена прострочена сума платежів'
      and cast(
        concat(
          '20',
          SUBSTRING_INDEX(month12Name, '/', -1),
          case when length(SUBSTRING_INDEX(month12Name, '/', 1)) = 1 then concat('0', SUBSTRING_INDEX(month12Name, '/', 1)) else SUBSTRING_INDEX(month12Name, '/', 1) end,
          '01'
        ) as date
      ) between aaa.dateApplication - INTERVAL 366 Day
      AND aaa.dateApplication
  ) '19.	Сума прострочки МФО за останній рік МБКІ',
  (
    SELECT
      sum(b.dlamtexp)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and (
        a.dldonor = 'FIN'
        or a.dldonor = 'MFO'
      )
      and b.dlds between b.dldpf - INTERVAL 367 day       and b.dldpf
      and a.dlamt <= 20000
       and dldateclc=(select max(dldateclc) from deallifeUBKI where  crdealid = b.crdealid and `dlflstat` in (1,5,2,3) and dlamtexp>0 )
     and exists(
        select
          max(id)
        from
          deallifeUBKI
        where
          crdealid = b.crdealid
          and   `dlflstat` not in (1,5) 
      )
          and b.dldateclc between aaa.dateApplication-interval 365 day and aaa.dateApplication 
  ) '19.	Сума прострочки МФО за останній рік УБКІ',
  (
    select
      max(
        ifnull(month1value, 0) + ifnull(month2value, 0) + ifnull(month3value, 0) + ifnull(month4value, 0) + ifnull(month5value, 0) + ifnull(month6value, 0) + ifnull(month7value, 0) + ifnull(month8value, 0) + ifnull(month9value, 0) + ifnull(month10value, 0) + ifnull(month11value, 0) + ifnull(month12value, 0)
      ) as cnt
    from
      contractsMBKI a
      join temp21 t on a.id = t.contractid
      join historicalcalendarsMBKI b on a.id = b.contractid
    where
      (
        (`CreditorType` = 'Фінансова компанія')
        or (
          `CreditorType` = 'Фінансова компанія - онлайн кредитування'
        )
        or (PurposeOfCredit = 'Кредит на карту')
      )
      and `TotalAmountValue` <= 20000
      and b.subjectid = aa.subjectid
      and (
        b.description = 'Сумарна кількіс'
        or b.description = 'Сумарна кількість просторочених платежів'
      )
  ) '8.1	Сумарна кількість прострочених виплат по кредитах МФО в МБКІ',
  (
    SELECT
      count(b.id)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and (
        a.dldonor = 'FIN'
        or a.dldonor = 'MFO'
      )
      and b.dlds between b.dldpf - INTERVAL 45 day
      and b.dldpf
      and a.dlamt <= 20000
      and dlamtexp > 0
      and dldayexp > 30
  ) '9.1	Сумарна кількість прострочених виплат по кредитах МФО в УБКІ',
  (
    SELECT
      count(dd.suma)
    FROM
      `contractsMBKI` a
      join temp21 t on a.id = t.contractid
	  join table60 dd on a.subjectid=dd.subjectid
    WHERE
       a.`SubjectId` = aa.subjectid
  ) '10.1	Сумарна кількість прострочених виплат по кредитах МФО в МБКІ поточних ',
  (
    SELECT
       sum(TIMESTAMPDIFF(MONTH,(select min(STR_TO_DATE(concat(dlmonth ,'/01/',dlyear ) ,'%m/%d/%Y')) from deallifeUBKI where crdealid=b.crdealid and STR_TO_DATE(concat(dlmonth ,'/01/',dlyear ) ,'%m/%d/%Y') =(
        select
          max(STR_TO_DATE(concat(dlmonth ,'/01/',dlyear ) ,'%m/%d/%Y'))  from
          deallifeUBKI
        where
          crdealid = b.crdealid and dlamtexp=0 and dldayexp<31)), STR_TO_DATE(concat(dlmonth ,'/01/',dlyear ) ,'%m/%d/%Y'))) 
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.dlamtexp > 0
      and b.cki_id = aa.ckiid
      and (
        a.dldonor = 'FIN'
        or a.dldonor = 'MFO'
      )
      and b.dlds between b.dldpf - INTERVAL 367 day
      and b.dldpf
      and a.dlamt <= 20000
     and STR_TO_DATE(concat(dlmonth ,'/01/',dlyear ) ,'%m/%d/%Y') =(
        select
          max(STR_TO_DATE(concat(dlmonth ,'/01/',dlyear ) ,'%m/%d/%Y'))
        from
          deallifeUBKI
        where
          crdealid = b.crdealid
      )
      and dldayexp > 30
      and b.id =(
        select
          max(id)
        from
          deallifeUBKI
        where
          crdealid = b.crdealid
      )
  ) '11.1	Сумарна кількість прострочених виплат по кредитах МФО в УБКІ поточних',
  (
    SELECT
      sum(DueInterestAmountValue)
    FROM
      `contractsMBKI` a
      join temp21 t on a.id = t.contractid
    WHERE
      (
        (`CreditorType` = 'Фінансова компанія')
        or (
          `CreditorType` = 'Фінансова компанія - онлайн кредитування'
        )
        or (PurposeOfCredit = 'Кредит на карту')
      )
      and `TotalAmountValue` <= 20000
      and `SubjectId` = aa.subjectid
  ) '12.1	Загальна сума прострочених виплат (поточних) по кредитах МФО в МБКІ',
  (
    SELECT
      sum(b.dlamtexp)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and (
        a.dldonor = 'FIN'
        or a.dldonor = 'MFO'
      )
      and b.dlds between b.dldpf - INTERVAL 367 day
      and b.dldpf
      and a.dlamt <= 20000
      and dldateclc =(
        select
          max(dldateclc)
        from
          deallifeUBKI
        where
          crdealid = b.crdealid
      )
      and b.id =(
        select
          max(id)
        from
          deallifeUBKI
        where
          crdealid = b.crdealid
      )
      and dldayexp > 30
  ) '13.1	Загальна сума прострочених виплат (поточних) по кредитах МФО в УБКІ ',
  (
    (
      (
        SELECT
          sum(distinct OverdueAmountValue)
        FROM
          `contractsMBKI` a
          join temp22 t on a.id = t.contractid
        WHERE
          not(
             (
              (`CreditorType` = 'Фінансова компанія')
              or (
                `CreditorType` = 'Фінансова компанія - онлайн кредитування'
              )
              or (PurposeOfCredit = 'Кредит на карту')
            )
            and `TotalAmountValue` <= 20000
          )
          and `SubjectId` = aa.subjectid
      )
    )
  ) '14.1	Сума поточних прострочених виплат по інших кредитах, НЕ кредитах МФО  в МБКІ',
  (
    (
      SELECT
        sum(b.dlamtexp)
      from
        crdealUBKI a
        join deallifeUBKI b on a.id = b.crdealid
      WHERE
        b.cki_id = aa.ckiid
        and dldateclc =(
          select
            max(dldateclc)
          from
            deallifeUBKI
          where
            crdealid = b.crdealid
        )
        and b.id =(
          select
            max(id)
          from
            deallifeUBKI
          where
            crdealid = b.crdealid
        )
        and dldayexp > 30
        and not(
          (
            a.dldonor = 'FIN'
            or a.dldonor = 'MFO'
          )
          and b.dlds between b.dldpf - INTERVAL 367 day
          and b.dldpf
          and a.dlamt <= 20000
        )
    )
  ) '15.1	Сума поточних прострочених виплат по інших кредитах, НЕ кредитах МФО  в УБКІ ',
  (
    (
      select
        max(
          GREATEST(
            ifnull(month1value, 0),
            ifnull(month2value, 0),
            ifnull(month3value, 0),
            ifnull(month4value, 0),
            ifnull(month5value, 0),
            ifnull(month6value, 0),
            ifnull(month7value, 0),
            ifnull(month8value, 0),
            ifnull(month9value, 0),
            ifnull(month10value, 0),
            ifnull(month11value, 0),
            ifnull(month12value, 0)
          )
        ) as cnt
      from
        contractsMBKI a
        join temp22 t on a.id = t.contractid
        join historicalcalendarsMBKI b on a.id = b.contractid
      where
        DueInterestAmountValue > 0
        and not(
          (
            (`CreditorType` = 'Фінансова компанія')
            or (
              `CreditorType` = 'Фінансова компанія - онлайн кредитування'
            )
            or (PurposeOfCredit = 'Кредит на карту')
          )
          and `TotalAmountValue` <= 20000
        )
        and b.subjectid = aa.subjectid
        and (
          b.description = 'Сумарна кількіс'
          or b.description = 'Сумарна кількість просторочених платежів'
        )
    )
  ) '16.1	Максимальний к-сть прострочених платежів по кредитах НЕ МФО, МБКІ',
  (
    SELECT
      max(b.dldayexp)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.dlamtexp > 0
      and b.cki_id = aa.ckiid
      and not(
        (
          a.dldonor = 'FIN'
          or a.dldonor = 'MFO'
        )
        and b.dlds between b.dldpf - INTERVAL 367 day
        and b.dldpf
        and a.dlamt <= 20000
      )
      and b.id =(
        select
          max(id)
        from
          deallifeUBKI
        where
          crdealid = b.crdealid
      )
      and dldayexp > 30
  ) '17.1	Максимальна к-сть днів поточної прострочки по кредитах НЕ МФО, УБКІ',
  (
    select
      sum(distinct 
        greatest(
          b.month1value,
          b.month2value,
          b.month3value,
          b.month4value,
          b.month5value,
          b.month6value,
          b.month7value,
          b.month8value,
          b.month9value,
          b.month10value,
          b.month11value,
          b.month12value
        )
      )/100
    from
      contractsMBKI a
      join temp22 t on a.id = t.contractid
      join historicalcalendarsMBKI b on a.id = b.contractid
    where
      (
         not(
          (`CreditorType` = 'Фінансова компанія')
          or (
            `CreditorType` = 'Фінансова компанія - онлайн кредитування'
          )
          or (PurposeOfCredit = 'Кредит на карту')
        )
        and `TotalAmountValue` <= 20000
      )
      and a.subjectid = aa.subjectid
     and cast(concat('20',SUBSTRING_INDEX(month12Name, '/', -1), case when length(SUBSTRING_INDEX(month12Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month12Name, '/', 1) ) else 
		SUBSTRING_INDEX(month12Name, '/', 1) end,'01') as date) between aaa.dateApplication - INTERVAL 360 Day AND aaa.dateApplication
  
      and b.type = '-12'
      and b.description = 'Несплачена прострочена сума платежів'
     
  ) '18.1	Сума прострочки не МФО за останній рік МБКІ',
  (
    SELECT
      sum(maxs)
    from
      temp23
    where
      cki_id = aa.ckiid
  ) '18.1	Сума прострочки не МФО за останній рік УБКІ',
  (
    select
      sum(
          b.month11value
        
      )/100
    from
      contractsMBKI a
      join temp21 t on a.id = t.contractid
      join historicalcalendarsMBKI b on a.id = b.contractid
    where
      (
         (
          (`CreditorType` = 'Фінансова компанія')
          or (
            `CreditorType` = 'Фінансова компанія - онлайн кредитування'
          )
          or (PurposeOfCredit = 'Кредит на карту')
        )
        and `TotalAmountValue` <= 20000
      )
      and a.subjectid = aa.subjectid
      and ContractType <> 'Existing'
      and b.type = '-12'
      and b.description = 'Несплачена прострочена сума платежів'
      and cast(
        concat(
          '20',
          SUBSTRING_INDEX(month12Name, '/', -1),
          case when length(SUBSTRING_INDEX(month12Name, '/', 1)) = 1 then concat('0', SUBSTRING_INDEX(month12Name, '/', 1)) else SUBSTRING_INDEX(month12Name, '/', 1) end,
          '01'
        ) as date
      ) between aaa.dateApplication - INTERVAL 366 Day
      AND aaa.dateApplication
  ) '19.1	Сума прострочки МФО за останній рік МБКІ',
  (
    SELECT
      sum(b.dlamtexp)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
      join  temp24 t on a.cki_id =t.cki_id
    WHERE
      b.cki_id = aa.ckiid
      and (
        a.dldonor = 'FIN'
        or a.dldonor = 'MFO'
      )
      and b.dlds between b.dldpf - INTERVAL 367 day       and b.dldpf
      and a.dlamt <= 10000
       and dldateclc=(select max(dldateclc) from deallifeUBKI where  crdealid = b.crdealid and `dlflstat` in (1,5,2,3) and dlamtexp>0 )
      and exists(
        select
          max(id)
        from
          deallifeUBKI
        where
          crdealid = b.crdealid
          and   `dlflstat` not in (1,5)
      )

  ) '19.1	Сума прострочки МФО за останній рік УБКІ',
  (
    select
      case when Zone = 'WhiteZone' then 1 else 0 end
    from
      historyLog
    where
      applicationid = aa.id
      and algId = 2
      and Text like '%УБКІ%'
  ) '21.	Співпадіння телефону з заявки, з телефоном у звіті УБКІ',
  (
    select
      count(distinct a.id)
    from
      contractsMBKI a
    where
      not(
         (
          (`CreditorType` = 'Фінансова компанія')
          or (
            `CreditorType` = 'Фінансова компанія - онлайн кредитування'
          )
          or (PurposeOfCredit = 'Кредит на карту')
        )
        and `TotalAmountValue` <= 20000
      )
      and a.subjectid = aa.subjectid
      and ContractEndDate between aaa.dateApplication - INTERVAL 12 Month
      AND aaa.dateApplication
      and ContractType <> 'Existing'
  ) '22.	Кількість закритих кредитів НЕ МФО за останній рік МБКІ',
  (
    SELECT
      count(distinct a.id)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and not(
        (
          a.dldonor = 'FIN'
          or a.dldonor = 'MFO'
        )
        and b.dlds between b.dldpf - INTERVAL 367 day
        and b.dldpf
        and a.dlamt <= 20000
      )
      and dlflstat = 2
      and dlds between aaa.dateApplication - INTERVAL 12 Month
      AND aaa.dateApplication
      and b.id =(
        select
          max(id)
        from
          deallifeUBKI
        where
          crdealid = b.crdealid
      )
  ) '23.	Кількість закритих кредитів НЕ МФО за останній рік УБКІ',
  (
    select
      count(distinct a.id)
    from
      contractsMBKI a
    where
      not(
        (
          (`CreditorType` = 'Фінансова компанія')
          or (
            `CreditorType` = 'Фінансова компанія - онлайн кредитування'
          )
          or (PurposeOfCredit = 'Кредит на карту')
        )
        and `TotalAmountValue` <= 20000
      )
      and a.subjectid = aa.subjectid
      and CreditStartDate between aaa.dateApplication - INTERVAL 12 Month
      AND aaa.dateApplication
  ) '24.	Кількість виданих кредитів НЕ МФО за останній рік МБКІ',
  (
    SELECT
      count(distinct a.id)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and not(
        (
          a.dldonor = 'FIN'
          or a.dldonor = 'MFO'
        )
        and b.dlds between b.dldpf - INTERVAL 367 day
        and b.dldpf
        and a.dlamt <= 20000
      )
      and dlds between aaa.dateApplication - INTERVAL 12 Month
      AND aaa.dateApplication
     
  ) '25.	Кількість виданих кредитів НЕ МФО за останній рік УБКІ',
  (
    select
      count(distinct a.id)
    from
      contractsMBKI a
    where
      TotalAmountValue >= 5000
      and not(
        
		  (
          (`CreditorType` = 'Фінансова компанія')
          or (
            `CreditorType` = 'Фінансова компанія - онлайн кредитування'
          )
          or (PurposeOfCredit = 'Кредит на карту')
        )
        and `TotalAmountValue` <= 20000
      )
      and a.subjectid = aa.subjectid
      and ContractEndDate between aaa.dateApplication - INTERVAL 12 Month
      AND sysdate()
      and ContractType <> 'Existing'
  ) '22. 5000	Кількість закритих кредитів НЕ МФО за останній рік МБКІ',
  (
    SELECT
      count(distinct a.id)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and a.dlamt >= 5000
      and not(
        (
          a.dldonor = 'FIN'
          or a.dldonor = 'MFO'
        )
        and b.dlds between b.dldpf - INTERVAL 367 day
        and b.dldpf
        and a.dlamt <= 20000
      )
      and dlflstat = 2
      and dlds between aaa.dateApplication - INTERVAL 12 Month
      AND aaa.dateApplication
      and b.id =(
        select
          max(id)
        from
          deallifeUBKI
        where
          crdealid = b.crdealid
      )
  ) '23. 5000	Кількість закритих кредитів НЕ МФО за останній рік УБКІ',
  (
    select
      count(distinct a.id)
    from
      contractsMBKI a
    where
      TotalAmountValue >= 5000
      and not(
         (
          (`CreditorType` = 'Фінансова компанія')
          or (
            `CreditorType` = 'Фінансова компанія - онлайн кредитування'
          )
          or (PurposeOfCredit = 'Кредит на карту')
        )
        and `TotalAmountValue` <= 20000
      )
      and a.subjectid = aa.subjectid
      and CreditStartDate between aaa.dateApplication - INTERVAL 12 Month
      AND aaa.dateApplication
  ) '24. 5000	Кількість виданих кредитів НЕ МФО за останній рік МБКІ',
  (
    SELECT
      count(distinct a.id)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
      and a.dlamt >= 5000
      and not(
        (
          a.dldonor = 'FIN'
          or a.dldonor = 'MFO'
        )
        and b.dlds between b.dldpf - INTERVAL 367 day
        and b.dldpf
        and a.dlamt <= 20000
      )
      and dlds between aaa.dateApplication - INTERVAL 12 Month
      AND aaa.dateApplication
    
  ) '25. 5000	Кількість виданих кредитів НЕ МФО за останній рік УБКІ',
  (
    SELECT
      max(a.CreditStartDate)
    from
      contractsMBKI a
    WHERE
          a.subjectid = aa.subjectid
       and    (
         (
          (`CreditorType` = 'Фінансова компанія')
          or (
            `CreditorType` = 'Фінансова компанія - онлайн кредитування'
          )
          or (PurposeOfCredit = 'Кредит на карту')
        )
        and `TotalAmountValue` <= 20000
      )
     
  ) '27.	Остання дата видачі кредиту МФО МБКІ',
  (
    SELECT
      max(b.dlds)
    from
      crdealUBKI a
      join deallifeUBKI b on a.id = b.crdealid
    WHERE
      b.cki_id = aa.ckiid
       and (
        (
          a.dldonor = 'FIN'
          or a.dldonor = 'MFO'
        )
        and b.dlds between b.dldpf - INTERVAL 367 day
        and b.dldpf
        and a.dlamt <= 20000
      )  
  ) '28.	Остання дата видачі кредиту МФО УБКІ'
from
  tempAll aa
  join Applications aaa on aa.id = aaa.id


 