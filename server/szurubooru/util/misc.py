import datetime
import re
from szurubooru.errors import ValidationError

def is_valid_email(email):
    ''' Return whether given email address is valid or empty. '''
    return not email or re.match(r'^[^@]*@[^@]*\.[^@]*$', email)

class dotdict(dict): # pylint: disable=invalid-name
    ''' dot.notation access to dictionary attributes. '''
    def __getattr__(self, attr):
        return self.get(attr)
    __setattr__ = dict.__setitem__
    __delattr__ = dict.__delitem__

def parse_time_range(value, timezone=datetime.timezone(datetime.timedelta())):
    ''' Return tuple containing min/max time for given text representation. '''
    one_day = datetime.timedelta(days=1)
    one_second = datetime.timedelta(seconds=1)

    value = value.lower()
    if not value:
        raise ValidationError('Empty date format.')

    if value == 'today':
        now = datetime.datetime.now(tz=timezone)
        return (
            datetime.datetime(now.year, now.month, now.day, 0, 0, 0),
            datetime.datetime(now.year, now.month, now.day, 0, 0, 0) \
                + one_day - one_second)

    if value == 'yesterday':
        now = datetime.datetime.now(tz=timezone)
        return (
            datetime.datetime(now.year, now.month, now.day, 0, 0, 0) - one_day,
            datetime.datetime(now.year, now.month, now.day, 0, 0, 0) \
                - one_second)

    match = re.match(r'^(\d{4})$', value)
    if match:
        year = int(match.group(1))
        return (
            datetime.datetime(year, 1, 1),
            datetime.datetime(year + 1, 1, 1) - one_second)

    match = re.match(r'^(\d{4})-(\d{1,2})$', value)
    if match:
        year = int(match.group(1))
        month = int(match.group(2))
        return (
            datetime.datetime(year, month, 1),
            datetime.datetime(year, month + 1, 1) - one_second)

    match = re.match(r'^(\d{4})-(\d{1,2})-(\d{1,2})$', value)
    if match:
        year = int(match.group(1))
        month = int(match.group(2))
        day = int(match.group(3))
        return (
            datetime.datetime(year, month, day),
            datetime.datetime(year, month, day + 1) - one_second)

    raise ValidationError('Invalid date format: %r.' % value)

def icase_unique(source):
    target = []
    target_low = []
    for source_item in source:
        if source_item.lower() not in target_low:
            target.append(source_item)
            target_low.append(source_item.lower())
    return target
